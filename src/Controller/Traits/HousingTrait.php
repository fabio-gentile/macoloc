<?php

namespace App\Controller\Traits;

use App\Entity\Housing;
use App\Entity\HousingImage;
use App\Factory\FileUploaderFactory;
use App\Repository\ChamberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\FormInterface;

trait HousingTrait
{
    /**
     * Remove housing
     * @param EntityManagerInterface $entityManager
     * @param FileUploaderFactory $fileUploaderFactory
     * @param Housing $housing
     * @return void
     */
    public function removeHousing(EntityManagerInterface $entityManager, FileUploaderFactory $fileUploaderFactory, Housing $housing): void
    {
        $fileUploader = $fileUploaderFactory->createUploader('housings');

        foreach ($housing->getHousingImages() as $housingImage) {
            if ($fileUploader->remove($housingImage->getFilename()))
                $entityManager->remove($housingImage);
        }

        foreach ($housing->getChambers() as $chamber) {
            $entityManager->remove($chamber);
        }

        $entityManager->remove($housing);
        $entityManager->flush();
    }

    /**
     * Edit housing
     * @param FormInterface $form
     * @param Housing $housing
     * @param FileUploaderFactory $fileUploaderFactory
     * @param EntityManagerInterface $entityManager
     * @param ChamberRepository $chamberRepository
     * @return void
     */
    public function editHousing(FormInterface $form, Housing $housing, FileUploaderFactory $fileUploaderFactory, EntityManagerInterface $entityManager, ChamberRepository $chamberRepository): void
    {
        $address = $form->get('address')->getData();
        $housingType = $form->get('housing')->getData();
        $chambers = $form->get('chambers')->getData();
        $commodity = $form->get('commodity')->getData();
        $other = $form->get('other')->getData();
        $description = $form->get('description')->getData();
        $images = $form->get('images')->getData();

        $housing
            ->setCommodity($commodity)
            ->setOther($other)
            ->setCity($address->getCity())
            ->setLatitude($address->getLatitude())
            ->setLongitude($address->getLongitude())
            ->setType($housingType['type_housing'])
            ->setSurfaceArea($housingType['surface_area'])
            ->setNumberOfRooms($housingType['number_of_rooms'])
            ->setTitle($description['title'])
            ->setDescription($description['description'])
            ->setUpdatedAt(new \DateTime())
        ;

        // Add chambers
        foreach ($chambers as $chamber) {
            if (null === $chamber->getId()) {
                // New chamber
                $housing->addChamber($chamber);
            }
        }

        // Remove chambers
        $originalChambers = $chamberRepository->findBy(['Housing' => $housing]);
        foreach ($originalChambers as $originalChamber) {
            if (!$chambers->contains($originalChamber)) {
                $housing->removeChamber($originalChamber);
                $entityManager->remove($originalChamber);
            }
        }

        // Add images
        foreach ($images as $image) {
            $imageEntity = new HousingImage();
            $fileUploader = $fileUploaderFactory->createUploader('housings'); // or 'housings'
            $result = $fileUploader->upload($image);
            $imageEntity
                ->setHousing($housing)
                ->setFilename($result['fileName'])
                ->setOriginalFilename($result['originalFilename'])
                ->setMimeType($result['mimeType'])
            ;

            $housing->addHousingImage($imageEntity);
        }

        $entityManager->persist($housing);
        $entityManager->flush();
    }

    /**
     * Delete housing image
     * @param EntityManagerInterface $entityManager
     * @param HousingImage $housingImage
     * @param FileUploaderFactory $fileUploaderFactory
     * @return void
     * @throws Exception
     */
    public function removeHousingImage(EntityManagerInterface $entityManager, HousingImage $housingImage, FileUploaderFactory $fileUploaderFactory): void {
        $fileUploader = $fileUploaderFactory->createUploader('housings');
        try {
            $fileUploader->remove($housingImage->getFilename());
            $entityManager->remove($housingImage);
            $entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.' . $e);
        }
    }
}
