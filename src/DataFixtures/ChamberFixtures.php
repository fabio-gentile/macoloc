<?php

namespace App\DataFixtures;

use App\Entity\Chamber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChamberFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $housing = $this->getReference(HousingFixtures::HOUSING_REFERENCE . $i);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $chamber = new Chamber();
                $price = rand(250, 750);
                $chamber->setPrice($price)
                    ->setAvaibleAt($faker->dateTimeBetween('-3 months', '+3 months'))
                    ->setCaution($price * 2)
                    ->setSurfaceArea(rand(10, 40))
                    ->setFurnished($faker->boolean())
                    ->setHousing($housing);

                $manager->persist($chamber);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            HousingFixtures::class,
        ];
    }
}
