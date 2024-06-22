<?php

namespace App\Controller\Admin;

use App\Data\Admin\SearchData;
use App\Entity\Housing;
use App\Entity\HousingImage;
use App\Factory\FileUploaderFactory;
use App\Form\Admin\SearchInputType;
use App\Form\EditHousingType;
use App\Repository\ChamberRepository;
use App\Repository\FrenchCityRepository;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Traits\HousingTrait;

#[Route('/admin/housings')]
class AdminHousingController extends AbstractController
{
    use HousingTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HousingRepository $housingRepository
    ) {}

    #[Route('/', name: 'admin_housing')]
    public function index(Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchInputType::class, $searchData);
        $form->handleRequest($request);
        $LIMIT = 10;

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->q = $form->get('q')->getData();
        }

        $searchData->page = $request->get('page', 1);
        $housings = $this->housingRepository->findSearchByUser($searchData, $LIMIT);
        return $this->render('admin/housing/index.html.twig', [
            'form' => $form,
            'housings' => $housings,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_housing_delete')]
    public function delete(
        Housing $housing,
        FileUploaderFactory $fileUploaderFactory,
    ): Response
    {
        $this->removeHousing($this->entityManager, $fileUploaderFactory, $housing);

        $this->addFlash('success', 'L\'annonce ' . $housing->getTitle() . ' a été supprimée.');
        return $this->redirectToRoute('admin_housing');
    }

    #[Route('/{id}/edit', name: 'admin_housing_edit')]
    public function edit(
        Housing $housing,
        FileUploaderFactory $fileUploaderFactory,
        Request $request,
        FrenchCityRepository $frenchCityRepository,
        ChamberRepository $chamberRepository
    ): Response
    {
        $form = $this->createForm(EditHousingType::class, [$housing, $frenchCityRepository->findOneBy(['city' => $housing->getCity()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editHousing($form, $housing, $fileUploaderFactory, $this->entityManager, $chamberRepository);

            $this->addFlash('success', 'L\'annonce ' . $housing->getTitle() . ' a été modifiée.');
            return $this->redirectToRoute('admin_housing');
        }

        return $this->render('admin/housing/edit.html.twig', [
            'form' => $form,
            'housing' => $housing,
            'errors' => $form->getErrors()
        ]);
    }

    #[Route('/{id}/image-delete', name: 'admin_housing_image_delete')]
    public function deleteImage(
        HousingImage $housingImage,
        FileUploaderFactory $fileUploaderFactory
    ): Response
    {
        $this->removeHousingImage($this->entityManager, $housingImage, $fileUploaderFactory);

        return $this->redirectToRoute('admin_housing_edit', ['id' => $housingImage->getHousing()->getId()]);
    }
}
