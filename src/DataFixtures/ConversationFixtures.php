<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\UserAccount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ConversationFixtures extends Fixture implements DependentFixtureInterface
{
    public const CONVERSATION_REFERENCE = 'conversation_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $conversation = new Conversation();
            $userOne = $this->getReference(UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 99));
            $userTwo = $this->getReference(UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 99));

            // Ensure userOne and userTwo are not the same
            while ($userOne === $userTwo) {
                $userTwo = $this->getReference(UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 99));
            }

            $conversation->setUserOne($userOne)
                ->setUserTwo($userTwo)
                ;

            $manager->persist($conversation);
            $this->addReference(self::CONVERSATION_REFERENCE . $i, $conversation);
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
