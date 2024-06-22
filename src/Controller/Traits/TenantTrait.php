<?php

namespace App\Controller\Traits;

use App\Entity\Tenant;
use App\Entity\TenantImage;
use App\Factory\FileUploaderFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\FormInterface;

trait TenantTrait
{
    /**
     * Remove tenant image
     * @param EntityManagerInterface $entityManager
     * @param FileUploaderFactory $fileUploaderFactory
     * @param TenantImage $tenantImage
     * @return void
     * @throws Exception
     */
    public function removeTenantImage(EntityManagerInterface $entityManager, FileUploaderFactory $fileUploaderFactory, TenantImage $tenantImage): void
    {
        $tenant = $tenantImage->getTenant();
        $fileUploader = $fileUploaderFactory->createUploader('tenants');
        try {
            $fileUploader->remove($tenantImage->getFilename());
            $tenant->setTenantImage(null);
            $entityManager->persist($tenant);
            $entityManager->remove($tenantImage);
            $entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.');
        }
    }

    /**
     * Remove tenant
     * @param EntityManagerInterface $entityManager
     * @param FileUploaderFactory $fileUploaderFactory
     * @param Tenant $tenant
     * @return void
     * @throws Exception
     */
    public function removeTenant(EntityManagerInterface $entityManager, FileUploaderFactory $fileUploaderFactory, Tenant $tenant): void
    {
        $image = $tenant->getTenantImage();
        if ($image) {
            $fileUploader = $fileUploaderFactory->createUploader('tenants');
            try {
                $fileUploader->remove($image->getFilename());
                $entityManager->remove($tenant->getTenantImage());
            } catch (Exception $e) {
                throw new Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.');
            }
        }
        $entityManager->remove($tenant);
        $entityManager->flush();
    }

    /**
     * Edit tenant
     * @param FormInterface $form
     * @param Tenant $tenant
     * @param FileUploaderFactory $fileUploaderFactory
     * @param EntityManagerInterface $entityManager
     * @return void
     * @throws Exception
     */
    public function editTenant(FormInterface $form, Tenant $tenant, FileUploaderFactory $fileUploaderFactory, EntityManagerInterface $entityManager): void
    {
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

        $entityManager->persist($tenant);
        $entityManager->flush();
    }
}
