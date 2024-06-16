<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Entity\UserImage;
use App\Factory\FileUploaderFactory;
use App\Form\AccountType;
use App\Form\UserImageType;
use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
class AccountController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
    )
    {}

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

            $this->manager->persist($user);
            $this->manager->flush();

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
        Request $request,
        UserAccountRepository $userRepository,
    ): Response
    {
        if ($request->isMethod('POST')) {
            $submittedToken = $request->getPayload()->get('token');
            if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
                $userRepository->removeUser($this->getUser());
                $request->getSession()->invalidate();
                $this->container->get('security.token_storage')->setToken(null);
                $this->addFlash('success', 'Votre compte a bien été supprimé.');
                return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', 'Une erreur est survenue. Veuillez réessayer.');
                return $this->redirectToRoute('app_account_delete', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('account/delete.html.twig', [

        ]);
    }

    #[Route('/image', name: 'app_account_image')]
    public function image(
        Request $request,
        FileUploaderFactory $fileUploaderFactory,
    ): Response {
        $form = $this->createForm(UserImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var UserAccount $user */
            $user = $this->getUser();
            $fileUploader = $fileUploaderFactory->createUploader('users');
            $hasChanged = false;

            $existingUserImage = $user->getUserImage();
            if ($existingUserImage) {
                $fileUploader->remove($existingUserImage->getFilename());

                $this->manager->remove($existingUserImage);
                $this->manager->flush();
                $hasChanged = true;
            }

            // Create a new UserImage entity
            $image = $form->get('image')->getData();
            if ($image) {
                $imageEntity = new UserImage();
                $result = $fileUploader->upload($image);
                $imageEntity
                    ->setUser($user)
                    ->setFilename($result['fileName'])
                    ->setOriginalFilename($result['originalFilename'])
                    ->setMimeType($result['mimeType']);

                $user->setUserImage($imageEntity);

                $this->manager->persist($imageEntity);
                $this->manager->flush();
            }

            $this->addFlash('success', $hasChanged ? 'Votre photo de profil a bien été modifiée.' : 'Votre photo de profil a bien été ajoutée.');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/image.html.twig', [
            'userImageForm' => $form->createView()
        ]);
    }

    #[Route('/image/delete', name: 'app_account_image_delete', methods: ['POST'])]
    public function deleteImage(
        Request $request,
        FileUploaderFactory $fileUploaderFactory,
    ) {
        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('delete-image', $submittedToken)) {
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        /* @var UserAccount $user */
        $user = $this->getUser();
        $userImage = $user->getUserImage();
        if ($userImage) {
            $fileUploader = $fileUploaderFactory->createUploader('users');
            $fileUploader->remove($userImage->getFilename());

            $this->manager->remove($userImage);
            $user->setUserImage(null);

            $this->manager->persist($user);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }
}
