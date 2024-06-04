<?php

namespace App\Controller;

use App\Data\SearchHousingData;
use App\Form\SearchHousingType;
use App\Repository\HousingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
//    #[Route('/housing', name: 'app_search_housing')]
//    public function housing(Request $request, HousingRepository $housingRepository): Response
//    {
//        $searchHousingData = new SearchHousingData();
//        $searchHousingData->page = $request->query->getInt('page', 1);
//        $form = $this->createForm(SearchHousingType::class, $searchHousingData);
//        $form->handleRequest($request);
//        $housings = $housingRepository->findSearch($searchHousingData);
//
//        return $this->render('search/housing.html.twig', [
//            'housings' => $housings,
//            'search_form' => $form->createView(),
//            'today' => new \DateTime()
//        ]);
//    }
//
//    #[Route('/housing/{city}', name: 'app_search_housing_city')]
//    public function housingCity(Request $request, HousingRepository $housingRepository): Response
//    {
//        $searchHousingData = new SearchHousingData();
//        $form = $this->createForm(SearchHousingType::class, $searchHousingData);
//        $form->handleRequest($request);
//        $housings = $housingRepository->findSearch($searchHousingData, $request->attributes->get('city'));
//
//        return $this->render('search/housing.html.twig', [
//            'housings' => $housings,
//            'search_form' => $form->createView(),
//            'today' => new \DateTime()
//        ]);
//    }

    #[Route('/housing', name: 'app_search_housing')]
    #[Route('/housing/{city}', name: 'app_search_housing_city', defaults: ['city' => null])]
    public function housing(Request $request, HousingRepository $housingRepository, ?string $city): Response
    {
        $searchHousingData = new SearchHousingData();
        $searchHousingData->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchHousingType::class, $searchHousingData);
        $form->handleRequest($request);
        $housings = $housingRepository->findSearch($searchHousingData, $city);

        return $this->render('search/housing.html.twig', [
            'housings' => $housings,
            'search_form' => $form->createView(),
            'today' => new \DateTime()
        ]);
    }

    #[Route('/tenant', name: 'app_search_tenant')]
    public function tenant(): Response
    {
        return $this->render('tenant/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
