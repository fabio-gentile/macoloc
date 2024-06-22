<?php

namespace App\Controller\Admin;

use App\Repository\HousingRepository;
use App\Repository\NewsletterReferenceRepository;
use App\Repository\NewsletterSubscriberRepository;
use App\Repository\TenantRepository;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        EntityManagerInterface $manager,
        HousingRepository $housingRepository,
        TenantRepository $tenantRepository,
        NewsletterSubscriberRepository $newsletterSubscriberRepository,
        UserAccountRepository $userRepository,
        ChartBuilderInterface $chartBuilder,
        NewsletterReferenceRepository $newsletterReferenceRepository
    ): Response
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $newsletterReferences = $newsletterReferenceRepository->countNewslettersByMonth();
        $dateNewsletterReferences = [];
        $newsletter = [];

        foreach ($newsletterReferences as $newsletterReference) {
            $monthName = $months[$newsletterReference['month']] . ' ' . $newsletterReference['year'];
            $dateNewsletterReferences[] = $monthName;
            $newsletter[] = $newsletterReference['count'];
        }

        $newsletterChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $newsletterChart->setData([
            'labels' => $dateNewsletterReferences,
            'datasets' => [
                [
                    'label' => 'Newsletters envoyées.',
                    'fill' => false,
                    'borderColor' => '#5146CE',
                    'tension' => 0.1,
                    'data' => $newsletter,
                ],
            ],
        ]);

        $users = $userRepository->countUsersByMonth();
        $dateUsers = [];
        $usersCount = [];

        foreach ($users as $user) {
            $monthName = $months[$user['month']] . ' ' . $user['year'];
            $dateUsers[] = $monthName;
            $usersCount[] = $user['count'];
        }

        $userChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $userChart->setData([
            'labels' => $dateUsers,
            'datasets' => [
                [
                    'label' => 'Utilisateurs inscrits.',
                    'fill' => false,
                    'borderColor' => '#5146CE',
                    'tension' => 0.1,
                    'data' => $usersCount,
                ],
            ],
        ]);

        $housings = $housingRepository->findAll();
        $tenants = $tenantRepository->findAll();

        $adsChart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $adsChart->setData([
            'labels' => ['Logements', 'Locataires'],
            'datasets' => [
                [
                    'label' => 'Nombre de logements et de locataires',
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                    'data' => [
                        count($housings),
                        count($tenants),
                    ],
                ],
            ],
        ]);



        return $this->render('admin/dashboard/index.html.twig', [
            'housings' => $housings,
            'tenants' => $tenants,
            'newsletterSubscribers' => $newsletterSubscriberRepository->findAll(),
            'users' => $userRepository->findAll(),
            'lastHousings' => $housingRepository->findLatest(),
            'lastTenants' => $tenantRepository->findLatest(),
            'lastNewsletterSubscribers' => $newsletterSubscriberRepository->findLatest(),
            'lastUsers' => $userRepository->findLatest(),
            'adsChart' => $adsChart,
            'newsletterChart' => $newsletterChart,
            'userChart' => $userChart,
        ]);
    }
}
