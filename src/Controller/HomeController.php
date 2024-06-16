<?php

namespace App\Controller;

use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        HousingRepository $housingRepository,
        TenantRepository $tenantRepository
    ): Response
    {
        return $this->render('home/index.html.twig', [
            'housings' => $housingRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'tenants' => $tenantRepository->findBy([], ['createdAt' => 'DESC'], 10),
            'today' => new \DateTime(),
        ]);
    }
}
