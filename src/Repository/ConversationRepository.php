<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\UserAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

//    /**
//     * @return Conversation[] Returns an array of Conversation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conversation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Find conversations for a user
     * @param UserAccount $user
     * @return array|null
     */
    public function findConversations(UserAccount $user): ?array
    {
        return $this->createQueryBuilder('c')
            ->where('c.userOne = :user')
            ->orWhere('c.userTwo = :user')
            ->setParameter('user', $user)
            ->leftJoin('c.messages', 'm')
            ->addSelect('MAX(m.createdAt) AS HIDDEN lastMessageCreatedAt')
            ->groupBy('c.id')
            ->orderBy('lastMessageCreatedAt', 'DESC')
            ->addOrderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find conversations between two users
     * @param UserAccount $userOne
     * @param UserAccount $userTwo
     * @return array|null
     */
    public function findConversationsWithUsers(UserAccount $userOne, UserAccount $userTwo): ?array
    {
        return $this->createQueryBuilder('c')
            ->where('c.userOne = :user  OR c.userTwo = :user')
            ->andWhere('c.userOne = :user2 OR c.userTwo = :user2')
            ->setParameter('user', $userOne)
            ->setParameter('user2', $userTwo)
            ->getQuery()
            ->getResult()
        ;
    }
}
