<?php

namespace App\Controller\Admin;

use App\Data\Admin\SearchUserData;
use App\Entity\FrenchCity;
use App\Entity\Tenant;
use App\Entity\TenantImage;
use App\Factory\FileUploaderFactory;
use App\Form\Admin\SearchInputType;
use App\Form\EditTenantType;
use App\Repository\FrenchCityRepository;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tenants')]
class AdminTenantController extends AbstractController
{
    public function __construct(
        private TenantRepository $tenantRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'admin_tenant')]
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
        $users = $this->tenantRepository->findSearchByUser($searchData, $LIMIT);

        $searchData->page = $request->get('page', 1);

        return $this->render('admin/tenant/index.html.twig', [
            'form' => $form,
            'tenants' => $users,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_tenant_delete')]
    public function delete(
        Tenant $tenant,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $image = $tenant->getTenantImage();

        if ($image) {
            $fileUploader = $fileUploaderFactory->createUploader('tenants');
            try {
                $fileUploader->remove($image->getFilename());
                $this->entityManager->remove($tenant->getTenantImage());
            } catch (\Exception $e) {
                throw new \Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.');
            }
        }

        $name = $tenant->getUser()->getFullName();

        $this->entityManager->remove($tenant);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le locataire ' . $name . ' a bien été supprimé.');
        return $this->redirectToRoute('admin_tenant');
    }

    #[Route('/{id}/edit', name: 'admin_tenant_edit')]
    public function edit(
        Tenant $tenant,
        Request $request,
        FileUploaderFactory $fileUploaderFactory,
        FrenchCityRepository $frenchCityRepository
    ): Response
    {
        $form = $this->createForm(EditTenantType::class, [$tenant, $frenchCityRepository->findOneBy(['city' => $tenant->getCity()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FrenchCity $address */
            $address = $form->get('address')->getData();

            $image = $form->get('image')->getData();
            $about = $form->get('about')->getData();

            $tenant
                ->setCity($address->getCity())
                ->setLatitude($address->getLatitude())
                ->setLongitude($address->getLongitude())
                ->setBudget($about['budget'])
                ->setDescription($form->get('description')->getData())
                ->setActivity($about['activity'])
                ->setGender($tenant->getUser()->getGender())
                ->setUpdatedAt(new \DateTime())
            ;

            if ($image) {
                $fileUploader = $fileUploaderFactory->createUploader('tenants');

                if ($tenant->getTenantImage()) {
                    try {
                        $fileUploader->remove($tenant->getTenantImage()->getFilename());
                        $this->entityManager->remove($tenant->getTenantImage());
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        throw new \Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.');
                    }

                }

                $result = $fileUploader->upload($image);
                $tenantImage = new TenantImage();
                $tenantImage
                    ->setFilename($result['fileName'])
                    ->setOriginalFilename($result['originalFilename'])
                    ->setMimeType($result['mimeType'])
                ;

                $tenant->setTenantImage($tenantImage);
            }

            $this->entityManager->persist($tenant);
            $this->entityManager->flush();

            $name = $tenant->getUser()->getFullName();
            $this->addFlash('success', 'Le locataire ' . $name . ' a bien été modifié.');
            return $this->redirectToRoute('admin_tenant');
        }

        return $this->render('admin/tenant/edit.html.twig', [
            'form' => $form,
            'tenant' => $tenant,
            'errors' => $form->getErrors()
        ]);
    }

    #[Route('/image-delete/{id}', name: 'admin_tenant_image_delete')]
    public function deleteImage(
        TenantImage $tenantImage,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $tenant = $tenantImage->getTenant();
        $fileUploader = $fileUploaderFactory->createUploader('tenants');
        try {
            $fileUploader->remove($tenantImage->getFilename());
            $tenant->setTenantImage(null);
            $this->entityManager->persist($tenant);
            $this->entityManager->remove($tenantImage);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.');
        }

        $this->addFlash('success', 'L\'image a bien été supprimée.');
        return $this->redirectToRoute('admin_tenant_edit', ['id' => $tenant->getId()]);
    }
}
