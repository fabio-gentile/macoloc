<?php

namespace App\DataFixtures;

use App\Entity\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\Address;
use Faker\Provider\fr_FR\Text;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TenantFixtures extends Fixture implements DependentFixtureInterface
{
    public const TENANT_REFERENCE = 'tenant_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Address($faker));
        $faker->addProvider(new Text($faker));

        $gender = ['Femme', 'Homme', 'Autre'];
        $activity = ['Étudiant', 'Salarié', 'Indépendant', 'Sans activité', 'Autre'];

        for ($i = 0; $i < 100; $i++) {
            $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);

            $tenant = new Tenant();
            $tenant->setCity($faker->city)
                ->setBudget(rand(250, 750))
                ->setDescription($faker->realTextBetween(150, 300))
                ->setGender($faker->randomElement($gender))
                ->setLatitude($faker->latitude())
                ->setLongitude($faker->longitude())
                ->setActivity($faker->randomElement($activity))
                ->setUser($user)
            ;

            $manager->persist($tenant);
            $this->addReference(self::TENANT_REFERENCE . $i, $tenant);
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
