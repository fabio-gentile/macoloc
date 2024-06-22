<?php

namespace App\Controller\Admin;

use App\Repository\HousingRepository;
use App\Repository\NewsletterSubscriberRepository;
use App\Repository\TenantRepository;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        EntityManagerInterface $manager,
        HousingRepository $housingRepository,
        TenantRepository $tenantRepository,
        NewsletterSubscriberRepository $newsletterSubscriberRepository,
        UserAccountRepository $userRepository
    ): Response
    {

        return $this->render('admin/dashboard/index.html.twig', [
            'housings' => $housingRepository->findAll(),
            'tenants' => $tenantRepository->findAll(),
            'newsletterSubscribers' => $newsletterSubscriberRepository->findAll(),
            'users' => $userRepository->findAll(),
            'lastHousings' => $housingRepository->findLatest(),
            'lastTenants' => $tenantRepository->findLatest(),
            'lastNewsletterSubscribers' => $newsletterSubscriberRepository->findLatest(),
            'lastUsers' => $userRepository->findLatest(),
        ]);
    }
}
