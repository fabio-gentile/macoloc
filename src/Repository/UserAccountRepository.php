<?php

namespace App\Repository;

use App\Entity\Tenant;
use App\Entity\UserAccount;
use App\Factory\FileUploaderFactory;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAccount>
 */
class UserAccountRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $manager,
        private FileUploaderFactory $fileUploaderFactory
    )
    {
        parent::__construct($registry, UserAccount::class);
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Find latest users
     * @param DateTime $yesterday
     * @return UserAccount[]|null
     */
    public function findLatest(DateTime $yesterday = new DateTime('-1 day')): ?array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->where('u.createdAt BETWEEN :yesterday AND :today')
            ->setParameter('yesterday', $yesterday)
            ->setParameter('today', new DateTime())
            ->andWhere('u.isVerified = true')
            ->getQuery()
            ->getResult();
    }

    public function removeUser(
        UserAccount $user
    ) : bool
    {
//        dd($user);
        foreach ($user->getTenants() as $tenant) {
            /** @var Tenant $tenant */
            $tenantImage = $tenant->getTenantImage();

            if ($tenantImage) {
                $fileUploader = $this->fileUploaderFactory->createUploader('tenants');
                if ($fileUploader->remove($tenantImage->getFilename()))
                    $this->manager->remove($tenantImage);
            }

            $this->manager->remove($tenant);
        }

        foreach ($user->getHousings() as $housing) {
            foreach ($housing->getHousingImages() as $housingImage) {
                $fileUploader = $this->fileUploaderFactory->createUploader('housings');
                if ($fileUploader->remove($housingImage->getFilename()))
                    $this->manager->remove($housingImage);
            }

            foreach ($housing->getChambers() as $chamber) {
                $this->manager->remove($chamber);
            }

            $this->manager->remove($housing);
        }

        // remove user image
        $userImage = $user->getUserImage();
        if ($userImage) {
            $fileUploader = $this->fileUploaderFactory->createUploader('users');
            if ($fileUploader->remove($userImage->getFilename()))
                $this->manager->remove($userImage);
        }

        // remove chat
        foreach ($user->getConversationsOne() as $conversationOne) {
            foreach ($conversationOne->getMessages() as $message) {
                $this->manager->remove($message);
            }

            $this->manager->remove($conversationOne);
        }

        foreach ($user->getConversationsTwo() as $conversationTwo) {
            foreach ($conversationTwo->getMessages() as $message) {
                $this->manager->remove($message);
            }

            $this->manager->remove($conversationTwo);
        }

        $this->manager->remove($user);
        $this->manager->flush();
        return true;
    }
}
