<?php

namespace App\Controller\Admin;

use App\Data\Admin\SearchData;
use App\Entity\Tenant;
use App\Entity\TenantImage;
use App\Factory\FileUploaderFactory;
use App\Form\Admin\SearchInputType;
use App\Form\EditTenantType;
use App\Repository\FrenchCityRepository;
use App\Repository\TenantRepository;
use App\Traits\TenantTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tenants')]
class AdminTenantController extends AbstractController
{
    use TenantTrait;

    public function __construct(
        private TenantRepository $tenantRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'admin_tenant')]
    public function index(
        Request $request,
    ): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchInputType::class, $searchData);
        $form->handleRequest($request);
        $LIMIT = 10;

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->q = $form->get('q')->getData();
        }

        $searchData->page = $request->get('page', 1);
        $users = $this->tenantRepository->findSearchByUser($searchData, $LIMIT);

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
        $this->removeTenant($this->entityManager, $fileUploaderFactory, $tenant);

        $this->addFlash('success', 'Le locataire ' . $tenant->getUser()->getFullname() . ' a bien été supprimé.');
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
            $this->editTenant($form, $tenant, $fileUploaderFactory, $this->entityManager);

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
        $this->removeTenantImage($this->entityManager, $fileUploaderFactory, $tenantImage);

        $this->addFlash('success', 'L\'image a bien été supprimée.');
        return $this->redirectToRoute('admin_tenant_edit', ['id' => $tenantImage->getTenant()->getId()]);
    }
}
