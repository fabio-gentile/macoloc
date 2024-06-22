<?php

namespace App\Repository;

use App\Entity\NewsletterReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsletterReference>
 */
class NewsletterReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterReference::class);
    }

    //    /**
    //     * @return NewsletterReference[] Returns an array of NewsletterReference objects
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

    //    public function findOneBySomeField($value): ?NewsletterReference
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     *  Count newsletters by month
     * @return array|null
     */
    public function countNewslettersByMonth(): ?array
    {
        return $this->createQueryBuilder('n')
            ->select('MONTH(n.sentAt) as month, YEAR(n.sentAt) as year, COUNT(n.id) as count')
            ->where('n.sentAt > :date')
            ->setParameter('date', new \DateTime('-1 year'))
            ->groupBy('year, month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
