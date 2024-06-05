<?php

namespace App\DataFixtures;

use App\Entity\TenantImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TenantImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $tenant = $this->getReference(TenantFixtures::TENANT_REFERENCE . $i);

            $tenantImage = new TenantImage();
            $tenantImage->setMimeType('image/jpeg')
                ->setFilename($faker->sha256() . '.jpeg')
                ->setOriginalFilename($faker->sha256() . '.jpeg')
                ->setTenant($tenant)
            ;

            $manager->persist($tenantImage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TenantFixtures::class,
        ];
    }
}
