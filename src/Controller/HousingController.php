<?php

namespace App\Controller;

use App\Controller\Traits\HousingTrait;
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
    use HousingTrait;

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
            $this->editHousing($form, $housing, $fileUploaderFactory, $this->entityManager, $this->chamberRepository);

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
        $this->removeHousing($this->entityManager, $fileUploaderFactory, $housing);

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
        $this->removeHousingImage($this->entityManager, $housingImage, $fileUploaderFactory);

        $this->addFlash('success', 'L\'image a bien été supprimée.');
        return $this->redirectToRoute('app_housing_edit', ['id' => $housingImage->getHousing()->getId()]);
    }
}
