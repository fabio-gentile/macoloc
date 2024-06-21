<?php

namespace App\Repository;

use App\Entity\NewsletterSubscriber;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsletterSubscriber>
 */
class NewsletterSubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterSubscriber::class);
    }

    //    /**
    //     * @return NewsletterSubscriber[] Returns an array of NewsletterSubscriber objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?NewsletterSubscriber
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Delete newsletter subscriber
     * @param NewsletterSubscriber $newsletterSubscriber
     */
    public function delete(NewsletterSubscriber $newsletterSubscriber): void
    {
        foreach ($newsletterSubscriber->getNewsletterReferences() as $reference) {
//            $newsletterSubscriber->removeNewsletterReference($reference);
            $this->getEntityManager()->remove($reference);
        }

        $this->getEntityManager()->remove($newsletterSubscriber);
        $this->getEntityManager()->flush();
    }

    /**
     * Find latest newsletter subscribers
     * @param DateTime $yesterday
     * @return NewsletterSubscriber[]|null
     */
    public function findLatest(DateTime $yesterday = new DateTime('-1 day')): ?array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.subscribedAt', 'DESC')
            ->where('n.subscribedAt BETWEEN :yesterday AND :today')
            ->setParameter('yesterday', $yesterday)
            ->setParameter('today', new DateTime())
            ->getQuery()
            ->getResult();
    }
}
