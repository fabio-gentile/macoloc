<?php

namespace App\Controller\Admin;

use App\Data\Admin\SearchUserData;
use App\Entity\UserAccount;
use App\Factory\FileUploaderFactory;
use App\Form\AccountType;
use App\Form\Admin\SearchInputType;
use App\Form\UserRemoveImageType;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin')]
class AdminUserController extends AbstractController
{
    public function __construct(
      private UserAccountRepository $userRepository
    ) {}

    #[Route('/users', name: 'admin_user')]
    public function index(
        Request $request,
    ): Response
    {
        $searchData = new SearchUserData();
        $form = $this->createForm(SearchInputType::class, $searchData);
        $form->handleRequest($request);
        $LIMIT = 10;

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->q = $form->get('q')->getData();
        }

        $searchData->page = $request->get('page', 1);
        $users = $this->userRepository->findSearch($searchData, $LIMIT);

        $searchData->page = $request->get('page', 1);
        return $this->render('admin/user/index.html.twig', [
            'form' => $form,
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'admin_user_delete', requirements: ['id' => Requirement::UUID_V4])]
    public function delete(UserAccount $user): Response
    {
        if ($user->isAdmin()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer un administrateur');
            return $this->redirectToRoute('admin_user');
        }

        $name = $user->getFullname();
        $this->userRepository->removeUser($user);

        $this->addFlash('success', 'Le compte a bien de ' . $name . ' été supprimé');
        return $this->redirectToRoute('admin_user');
    }

    #[Route('/users/{id}/edit', name: 'admin_user_edit', requirements: ['id' => Requirement::UUID_V4])]
    public function edit(
        UserAccount $user,
        Request $request,
        FileUploaderFactory $fileUploaderFactory,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(AccountType::class, $user, [
            'roles' => [
                'Administrateur' => 'ROLE_ADMIN',
            ],
        ]);
        $form->handleRequest($request);
        $imageRemoveForm = $this->createForm(UserRemoveImageType::class);
        $imageRemoveForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Le compte de ' . $user->getFullname() . ' a bien été modifié');
            return $this->redirectToRoute('admin_user');
        }

        if ($imageRemoveForm->isSubmitted() && $imageRemoveForm->isValid()) {
            $userImage = $user->getUserImage();

            if ($userImage) {
                $fileUploader = $fileUploaderFactory->createUploader('users');
                $fileUploader->remove($userImage->getFilename());

                $manager->remove($userImage);
                $user->setUserImage(null);

                $manager->persist($user);
                $manager->flush();
            }

            $this->addFlash('success', 'L\'image de profile de '. $user->getFullname() . ' a bien été supprimée');
            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'imageRemoveForm' => $imageRemoveForm,
        ]);
    }

    #[Route('/users/{id}', name: 'admin_user_show', requirements: ['id' => Requirement::UUID_V4])]
    public function show(UserAccount $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
