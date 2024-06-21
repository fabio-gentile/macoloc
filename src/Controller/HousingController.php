<?php

namespace App\Controller;

use App\Entity\FrenchCity;
use App\Entity\Housing;
use App\Entity\HousingImage;
use App\Factory\FileUploaderFactory;
use App\Form\EditHousingType;
use App\Repository\ChamberRepository;
use App\Repository\FrenchCityRepository;
use App\Security\Voter\HousingVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/housing')]
class HousingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FrenchCityRepository $frenchCityRepository,
        private readonly ChamberRepository $chamberRepository
    ) {}

    #[Route('/{id}/', name: 'app_housing', requirements: ['id' => Requirement::UUID_V4])]
    public function index(Housing $housing): Response
    {
        return $this->render('housing/index.html.twig', [
            'housing' => $housing,
            'chambers' => $this->chamberRepository->findBy(['Housing' => $housing], ['avaibleAt' => 'ASC'])
        ]);
    }

    #[Route('/{id}/edit', name: 'app_housing_edit', requirements: ['id' => Requirement::UUID_V4])]
    #[isGranted(HousingVoter::EDIT, subject: 'housing')]
    public function edit(
        Housing $housing,
        FileUploaderFactory $fileUploaderFactory,
        Request $request
    ): Response
    {
        $form = $this->createForm(EditHousingType::class, [$housing, $this->frenchCityRepository->findOneBy(['city' => $housing->getCity()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FrenchCity $address */
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
            $originalChambers = $this->chamberRepository->findBy(['Housing' => $housing]);
            foreach ($originalChambers as $originalChamber) {
                if (!$chambers->contains($originalChamber)) {
                    $housing->removeChamber($originalChamber);
                    $this->entityManager->remove($originalChamber);
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

            $this->entityManager->persist($housing);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_housing', ['id' => $housing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publish/housing.html.twig', [
            'housing' => $housing,
            'form' => $form,
            'errors' => $form->getErrors()
        ]);
    }

    #[Route('/{id}/delete', name: 'app_housing_delete', requirements: ['id' => Requirement::UUID_V4])]
    #[isGranted(HousingVoter::DELETE, subject: 'housing')]
    public function delete(
        Housing $housing,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $fileUploader = $fileUploaderFactory->createUploader('housings');

        foreach ($housing->getHousingImages() as $housingImage) {
            if ($fileUploader->remove($housingImage->getFilename()))
                $this->entityManager->remove($housingImage);
        }

        foreach ($housing->getChambers() as $chamber) {
            $this->entityManager->remove($chamber);
        }

        $this->entityManager->remove($housing);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre annonce a bien été supprimée.');
        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/image-delete', name: 'app_housing_image_delete', requirements: ['id' => Requirement::UUID_V4])]
    #[isGranted(HousingVoter::DELETE, subject: 'housingImage')]
    public function deleteImage(
        HousingImage $housingImage,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        /** @var Housing $housing */
        $housing = $housingImage->getHousing();
        $fileUploader = $fileUploaderFactory->createUploader('housings');
        try {
            $fileUploader->remove($housingImage->getFilename());
            $this->entityManager->remove($housingImage);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la suppression de l\'image. Veuillez réessayer.' . $e);
        }

        $this->addFlash('success', 'L\'image a bien été supprimée.');
        return $this->redirectToRoute('app_housing_edit', ['id' => $housing->getId()]);
    }
}
