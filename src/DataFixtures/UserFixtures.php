<?php

namespace App\DataFixtures;

use App\Entity\UserAccount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\Person;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user_';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Person($faker));

        $gender = ['Homme', 'Femme', 'Autre'];

        $dateFrom = new \DateTimeImmutable('-1 years');
        $dateTo = new \DateTimeImmutable('now');
        $timestampFrom = $dateFrom->getTimestamp();
        $timestampTo = $dateTo->getTimestamp();
        $randomTimestamp = mt_rand($timestampFrom, $timestampTo);
        $randomDate = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);

        $user = new UserAccount();
        $user->setEmail('admin@macoloc.fr')
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setDateOfBirth($faker->dateTimeBetween('-50 years', '-18 years'))
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'password'
                )
            )
            ->setVerified(true)
            ->setGender($faker->randomElement($gender))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt($randomDate)
        ;

        $manager->persist($user);

        for ($i = 0; $i < 100; $i++) {
            $randomTimestamp = mt_rand($timestampFrom, $timestampTo);
            $randomDate = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);
            $user = new UserAccount();
            $user->setEmail($faker->email())
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setDateOfBirth($faker->dateTimeBetween('-50 years', '-18 years'))
                ->setPassword(
                    $this->passwordHasher->hashPassword(
                        $user,
                        'password'
                    )
                )
                ->setVerified(true)
                ->setGender($faker->randomElement($gender))
                ->setCreatedAt($randomDate)
            ;

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        $manager->flush();
    }
}
