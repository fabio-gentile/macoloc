<?php

namespace App\DataFixtures;

use App\Entity\HousingImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HousingImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $housing = $this->getReference(HousingFixtures::HOUSING_REFERENCE . $i);

            for ($k = 0; $k < rand(1, 5); $k++) {
                $housingImage = new HousingImage();

                $housingImage->setMimeType('image/jpeg')
                    ->setFilename($faker->sha256() . '.jpeg')
                    ->setOriginalFilename($faker->sha256() . '.jpeg')
                    ->setHousing($housing);

                $manager->persist($housingImage);
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
