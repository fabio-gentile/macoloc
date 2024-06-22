<?php

namespace App\Controller;

use App\Controller\Traits\TenantTrait;
use App\Entity\Tenant;
use App\Factory\FileUploaderFactory;
use App\Form\EditTenantType;
use App\Repository\FrenchCityRepository;
use App\Security\Voter\TenantVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tenant/{id}', requirements: ['id' => Requirement::UUID_V4])]
class TenantController extends AbstractController
{
    use TenantTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FrenchCityRepository   $frenchCityRepository
    ) {}

    #[Route('/', name: 'app_tenant')]
    public function index(Tenant $tenant): Response
    {
        return $this->render('tenant/index.html.twig', [
            'tenant' => $tenant
        ]);
    }

    #[Route('/edit', name: 'app_tenant_edit')]
    #[isGranted(TenantVoter::EDIT, subject: 'tenant')]
    public function edit(
        Tenant $tenant,
        Request $request,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $form = $this->createForm(EditTenantType::class, [$tenant, $this->frenchCityRepository->findOneBy(['city' => $tenant->getCity()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editTenant($form, $tenant, $fileUploaderFactory, $this->entityManager);

            return $this->redirectToRoute('app_tenant', ['id' => $tenant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publish/tenant.html.twig', [
            'tenant' => $tenant,
            'form' => $form,
            'errors' => $form->getErrors()
        ]);
    }

    #[Route('/delete', name: 'app_tenant_delete')]
    #[isGranted(TenantVoter::DELETE, subject: 'tenant')]
    public function delete(
        Tenant $tenant,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $this->removeTenant($this->entityManager, $fileUploaderFactory, $tenant);

        $this->addFlash('success', 'Votre annonce a bien été supprimée.');
        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image-delete', name: 'app_tenant_image_delete')]
    #[isGranted(TenantVoter::DELETE, subject: 'tenant')]
    public function deleteImage(
        Tenant $tenant,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $this->removeTenantImage($this->entityManager, $fileUploaderFactory, $tenant->getTenantImage());

        $this->addFlash('success', 'L\'image a bien été supprimée.');
        return $this->redirectToRoute('app_tenant_edit', ['id' => $tenant->getId()]);
    }
}
