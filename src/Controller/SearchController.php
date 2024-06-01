<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
    #[Route('/housing', name: 'app_search_housing')]
    public function housing(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
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
