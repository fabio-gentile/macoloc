<?php

namespace App\DataFixtures;

use App\Entity\NewsletterSubscriber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NewsletterSubscriberFixtures extends Fixture implements DependentFixtureInterface
{
    public const NEWSLETTER_SUBSCRIBER_REFERENCE = 'newsletter_subscriber_';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $newsletterSubscriber = new NewsletterSubscriber();
            $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);

            $newsletterSubscriber
                ->setEmail($user->getEmail())
                ->setLastSentAt(new \DateTime())
                ->setVerified(true)
            ;

            $manager->persist($newsletterSubscriber);
            $this->addReference(self::NEWSLETTER_SUBSCRIBER_REFERENCE . $i, $newsletterSubscriber);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
