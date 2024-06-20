<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_profile', requirements: ['id' => Requirement::UUID_V4] )]
    public function index(
        UserAccount $user,
        HousingRepository $housingRepository,
        TenantRepository $tenantRepository
    ): Response
    {
        $housings = $housingRepository->findBy(['user' => $user]);
        $tenants = $tenantRepository->findBy(['user' => $user]);

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'housings' => $housings,
            'tenants' => $tenants,
            'today' => new \DateTime(),
            'totalAds' => count($housings) + count($tenants)
        ]);
    }
}
