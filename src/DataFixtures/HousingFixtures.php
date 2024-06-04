<?php

namespace App\DataFixtures;

use App\Entity\Housing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\Address;
use Faker\Provider\fr_FR\Text;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HousingFixtures extends Fixture implements DependentFixtureInterface
{
    public const HOUSING_REFERENCE = 'housing_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Address($faker));
        $faker->addProvider(new Text($faker));

        $housingTypes = ['Maison', 'Appartement', 'Studio', 'Duplex', 'Villa', 'Résidence étudiante'];
        $commodities = ['Wi-Fi', 'Meublé', 'Parking', 'Ascenseur', 'Machine à laver', 'Lave-vaisselle', 'Climatisation', 'Garage', 'Jardin', 'Piscine'];
        $other = ['Fumeurs', 'Animaux'];

        for ($i = 0; $i < 100; $i++) {
            $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);

            $housing = new Housing();
            $housing->setStreet($faker->streetAddress())
                ->setPrice(rand(250, 750))
                ->setType($faker->randomElement($housingTypes))
                ->setTitle($faker->realTextBetween(10, 20))
                ->setNumberOfRooms(rand(1, 10))
                ->setSurfaceArea(rand(20, 200))
                ->setDescription($faker->realTextBetween(100, 200))
                ->setCommodity($faker->randomElements($commodities, rand(0, count($commodities) - 4)))
                ->setOther($faker->randomElements($other, rand(0, count($other))))
                ->setLatitude($faker->latitude())
                ->setLongitude($faker->longitude())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city())
                ->setUser($user)
            ;

            $manager->persist($housing);
            $this->addReference(self::HOUSING_REFERENCE . $i, $housing);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
