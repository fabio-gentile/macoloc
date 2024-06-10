<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Form\AccountType;
use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [

        ]);
    }

    #[Route('/ads', name: 'app_account_ads')]
    public function ads(
        TenantRepository $tenantRepository,
        HousingRepository $housingRepository
    ): Response
    {
        /* @var UserAccount $user */
        $user = $this->getUser();
        $housings = $housingRepository->findBy(['user' => $user]);
        $tenants = $tenantRepository->findBy(['user' => $user]);
        return $this->render('account/ads.html.twig', [
            'housings' => $housings,
            'tenants' => $tenants,
            'today' => new \DateTime(),
            'totalAds' => count($housings) + count($tenants)
        ]);
    }

    #[Route('/settings', name: 'app_account_settings')]
    public function settings(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $manager
    ): Response
    {
        /* @var UserAccount $user */
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('newPassword')->getData()) {
                if (!$passwordHasher->isPasswordValid($user, $form->get('currentPassword')->getData())) {
                    $form->get('currentPassword')->addError(new FormError('Le mot de passe actuel est incorrect'));
                    return $this->render('account/settings.html.twig', [
                        'form' => $form
                    ]);
                }

                if (strlen($form->get('newPassword')->getData()) < 6) {
                    $form->get('newPassword')->addError(new FormError('Le mot de passe doit contenir au moins 6 caractères'));
                    return $this->render('account/settings.html.twig', [
                        'form' => $form
                    ]);
                }

                if (strlen($form->get('newPassword')->getData()) > 100) {
                    $form->get('newPassword')->addError(new FormError('Le mot de passe doit contenir au maximum 100 caractères'));
                    return $this->render('account/settings.html.twig', [
                        'form' => $form
                    ]);
                }

                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
            }

            $user->setUpdatedAt(new \DateTime());

            $manager->persist($user);
            $manager->flush();

//            TODO: optionnellement, envoyer un email de confirmation de changement de mot de passe
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('account/settings.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/delete', name: 'app_account_delete')]
    public function delete(
        EntityManagerInterface $manager,
        Request $request,
        UserAccountRepository $userRepository
    ): Response
    {
        if ($request->isMethod('POST')) {
            $submittedToken = $request->getPayload()->get('token');
            if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
                $userRepository->removeUser($this->getUser());
                $this->addFlash('success', 'Votre compte a bien été supprimé.');
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', 'Une erreur est survenue. Veuillez réessayer.');
                return $this->redirectToRoute('app_account_delete', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('account/delete.html.twig', [

        ]);
    }
}
