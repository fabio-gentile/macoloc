<?php

namespace App\Service;

use App\Entity\NewsletterReference;
use App\Repository\HousingRepository;
use App\Repository\NewsletterSubscriberRepository;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;


class NewsletterService extends AbstractController {
    public function __construct(
        private NewsletterSubscriberRepository $newsletterSubscriberRepository,
        private MailerInterface $mailer,
        private HousingRepository $housingRepository,
        private TenantRepository $tenantRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function sendNewsletter(): void
    {
        $housings = $this->housingRepository->findLatest();
        $tenants = $this->tenantRepository->findLatest();
        if (empty($housings) && empty($tenants)) {
            return;
        }

        $subscribers = $this->newsletterSubscriberRepository->findBy(['isVerified' => true]);
        foreach ($subscribers as $subscriber) {
            $newsletterReference = new NewsletterReference();
            $newsletterReference->setSubscriber($subscriber);
            $newsletterReference->setSentAt(new \DateTime());
            $subscriber->addNewsletterReference($newsletterReference);
            $this->entityManager->flush();
            //TODO: mettre en page
            $email = (new TemplatedEmail())
                ->from($this->getParameter('no_reply_email'), 'Newsletter')
                ->to($subscriber->getEmail())
                ->subject('Dernières annonces de Macoloc')
                ->htmlTemplate('emails/newsletter.html.twig')
                ->context([
                    'housings' => $housings,
                    'tenants' => $tenants,
                    'newsletter_token' => $newsletterReference->getId(),
                    'unsubscribe_token' => $newsletterReference->getUnsubscribeToken(),
                ]);

            $this->mailer->send($email);
        }
    }
}
