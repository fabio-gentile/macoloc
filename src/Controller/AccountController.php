<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\HousingRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [

        ]);
    }

    #[Route('/ads', name: 'app_account_ads')]
    public function ads(
        TenantRepository $tenantRepository,
        HousingRepository $housingRepository
    ): Response
    {
        /* @var User $user */
        $user = $this->getUser();
        $housings = $housingRepository->findBy(['user' => $user]);
        $tenants = $tenantRepository->findBy(['user' => $user]);
        return $this->render('account/ads.html.twig', [
            'housings' => $housings,
            'tenants' => $tenants,
            'today' => new \DateTime(),
            'totalAds' => count($housings) + count($tenants)
        ]);
    }
}
