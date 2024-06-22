<?php

namespace App\DataFixtures;

use App\Entity\NewsletterReference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NewsletterReferenceFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $newsletterSubscriber = $this->getReference(NewsletterSubscriberFixtures::NEWSLETTER_SUBSCRIBER_REFERENCE . $i);

            for ($j = 0; $j < rand(40, 80); $j++) {
                $date = $faker->dateTimeBetween('-2 year', 'now');
                $newsletterReference = new NewsletterReference();
                $newsletterReference->setSubscriber($newsletterSubscriber)
                    ->setSentAt($date);

                $manager->persist($newsletterReference);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NewsletterSubscriberFixtures::class,
        ];
    }
}
