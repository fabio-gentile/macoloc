<?php

namespace App\Controller;

use App\Data\SearchHousingData;
use App\Data\SearchTenantData;
use App\Form\SearchHousingType;
use App\Form\SearchTenantType;
use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
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
            'search_form' => $form,
            'today' => new \DateTime(),
            'city' => $city ?? 'Toutes les villes',
        ]);
    }

    #[Route('/tenant', name: 'app_search_tenant')]
    #[Route('/tenant/{city}', name: 'app_search_tenant_city', defaults: ['city' => null])]
    public function tenant(Request $request, TenantRepository $tenantRepository, ?string $city): Response
    {
        $searchTenantData = new SearchTenantData();
        $searchTenantData->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchTenantType::class, $searchTenantData);
        $form->handleRequest($request);
        $tenants = $tenantRepository->findSearch($searchTenantData, $city);

        return $this->render('search/tenant.html.twig', [
            'tenants' => $tenants,
            'search_form' => $form->createView(),
            'city' => $city ?? 'Toutes les villes',
        ]);
    }
}
