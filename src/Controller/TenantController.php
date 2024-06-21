<?php

namespace App\Controller;

use App\Entity\FrenchCity;
use App\Entity\Tenant;
use App\Entity\TenantImage;
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

        $this->entityManager->remove($tenant);
        $this->entityManager->flush();

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
        $tenantImage = $tenant->getTenantImage();
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
        return $this->redirectToRoute('app_tenant_edit', ['id' => $tenant->getId()]);
    }
}
