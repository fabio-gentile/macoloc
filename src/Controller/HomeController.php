<?php

namespace App\Controller;

use App\Repository\HousingRepository;
use App\Repository\NewsletterSubscriberRepository;
use App\Repository\TenantRepository;
use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        HousingRepository $housingRepository,
        TenantRepository $tenantRepository,
        NewsletterSubscriberRepository $newsletterSubscriberRepository
    ): Response
    {
        $user = $this->getUser();

        $isSubscribedNewsletter =
            $user && boolval($newsletterSubscriberRepository->findOneBy(['email' => $user->getEmail()]));

        return $this->render('home/index.html.twig', [
            'housings' => $housingRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'tenants' => $tenantRepository->findBy([], ['createdAt' => 'DESC'], 10),
            'today' => new \DateTime(),
            'isSubscribedNewsletter' => !$isSubscribedNewsletter,
        ]);
    }

//    TODO: TMP remove
    #[Route('/about', name: 'app_about')]
    public function about(HousingRepository $housingRepository, TenantRepository $tenantRepository, NewsletterService $service): Response
    {
        $service->sendNewsletter();
        return $this->render('emails/newsletter.html.twig', [
            'unsubscribe_token' => Uuid::v4(),
            'newsletter_token' => Uuid::v4(),
            'housings' => $housingRepository->findLatest(),
            'tenants' => $tenantRepository->findLatest()
        ]);
    }
}
