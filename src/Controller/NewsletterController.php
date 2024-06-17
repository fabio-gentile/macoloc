<?php

namespace App\Controller;

use App\Repository\NewsletterReferenceRepository;
use App\Repository\NewsletterSubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/unsubscribe', name: 'app_newsletter_unsubscribe')]
    public function index(
        Request $request,
        NewsletterReferenceRepository $newsletterReferenceRepository,
        NewsletterSubscriberRepository $newsletterSubscriberRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $newsletterReference = $newsletterReferenceRepository->findOneBy([
                'unsubscribeToken' => $request->query->get('ut'),
                'id' => $request->query->get('nt')
            ]);

        if (!$newsletterReference) {
            return $this->redirectToRoute('app_homepage');
        }

        $subscriber = $newsletterReference->getSubscriber();

        foreach ($subscriber->getNewsletterReferences() as $newsletterReference) {
                $entityManager->remove($newsletterReference);
        }

        $entityManager->remove($subscriber);
        $entityManager->flush();

        return $this->render('newsletter/index.html.twig', [
        ]);
    }
}
