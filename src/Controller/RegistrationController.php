<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Form\RegistrationFormType;
use App\Form\RegistrationProfileType;
use App\Repository\UserAccountRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $user = new UserAccount();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_REGISTRATION_EMAIL_WAITING']);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            //TODO: mettre en page
            $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($this->getParameter('no_reply_email')))
                    ->to($user->getEmail())
                    ->subject('S\'il vous plaît, confirmez votre email.')
                    ->htmlTemplate('emails/registration/index.html.twig')
            );

            return $this->redirectToRoute('app_register_verify');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify', name: 'app_register_verify')]
    public function verify(Request $request): Response
    {
        $user = $this->getUser();

        if ($user && !in_array('ROLE_REGISTRATION_EMAIL_WAITING', $user->getRoles())) {
            return $this->redirectToRoute('app_account');
        }

        return $this->render('reset_password/check_email.html.twig');
    }

    #[Route('/verify/email', name: 'app_register_verify_email')]
    public function verifyUserEmail(Request $request, UserAccountRepository $userRepository, EntityManagerInterface $entityManager, Security $security, TranslatorInterface $translator): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        if ($user->isVerified()) {
            return $this->redirectToRoute('app_account');
        }

        // validate email confirmation link, sets User isVerified=true
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', ('Le lien pour vérifier votre email a expiré. Veuillez vous re-connecter pour obtenir un nouveau lien.'));

            return $this->redirectToRoute('app_register');
        }

        $user->setRoles(['ROLE_REGISTRATION_WAITING']);
        $entityManager->persist($user);
        $entityManager->flush();

        return $security->login($user, 'form_login', 'main');
    }

    #[Route('/profile', name: 'app_register_profile')]
    #[IsGranted('ROLE_REGISTRATION_WAITING')]
    public function profile(Request $request, EntityManagerInterface $entityManager, UserInterface $user, Security $security): Response
    {
        $form = $this->createForm(RegistrationProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $user UserAccount */
            $user->setFirstname($form->get('firstname')->getData())
                ->setLastname($form->get('lastname')->getData())
                ->setGender($form->get('gender')->getData())
                ->setDateOfBirth($form->get('dateOfBirth')->getData())
                ->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $security->login($user, 'form_login', 'main');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('registration/profile.html.twig', [
            'profileForm' => $form,
        ]);
    }
}
