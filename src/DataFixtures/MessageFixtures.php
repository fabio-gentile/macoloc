<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\UserAccount;
use App\Entity\Conversation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 200; $i++) {
            $message = new Message();
            $conversation = $this->getReference(ConversationFixtures::CONVERSATION_REFERENCE . $faker->numberBetween(0, 49));
            $sender = $faker->randomElement([
                $conversation->getUserOne(),
                $conversation->getUserTwo()
            ]);

            $message->setConversation($conversation)
                ->setSender($sender)
                ->setContent($faker->text(200))
                ;

            $manager->persist($message);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ConversationFixtures::class,
        ];
    }
}
