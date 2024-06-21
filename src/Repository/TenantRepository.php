<?php

namespace App\Repository;

use App\Data\Admin\SearchUserData;
use App\Data\SearchHousingData;
use App\Data\SearchTenantData;
use App\Entity\Tenant;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Tenant>
 */
class TenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Tenant::class);
    }

    //    /**
    //     * @return Tenant[] Returns an array of Tenant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tenant
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Find tenants by search data
     * @param SearchUserData $searchData
     * @param int $limit
     * @return PaginationInterface
     */
    public function findSearchByUser(SearchUserData $searchData, int $limit = 7): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if ($searchData->q) {
            $query = strtolower($searchData->q);
            $queryBuilder
                ->join('t.user', 'u')
                ->andWhere('LOWER(u.firstname) LIKE :query')
                ->orWhere('LOWER(u.lastname) LIKE :query')
                ->setParameter('query', "%{$query}%");
        }

        $queryBuilder->orderBy('t.createdAt', 'DESC');

        return $this->paginator->paginate(
            $queryBuilder,
            $searchData->page,
            $limit
        );
    }

    /**
     * Find latest tenants
     * @param DateTime $yesterday
     * @return Tenant[]|null
     */
    public function findLatest(DateTime $yesterday = new DateTime('-1 day')): ?array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->where('t.createdAt BETWEEN :yesterday AND :today')
            ->setParameter('yesterday', $yesterday)
            ->setParameter('today', new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tenants by filters
     * @param SearchTenantData $data
     * @param string|null $city
     * @param int $limit
     * @return PaginationInterface
     */
    public function findSearch(SearchTenantData $data, string $city = null, int $limit = 7): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if ($data->activity) {
            $queryBuilder->andWhere('t.activity IN (:activity)')
                ->setParameter('activity', $data->activity);
        }

        if ($data->gender) {
            $queryBuilder->andWhere('t.gender = :gender')
                ->setParameter('gender', $data->gender);
        }

        if ($city) {
            $queryBuilder->andWhere('t.city = :city')
                ->setParameter('city', $city);
        }

        if ($data->min_price) {
            $queryBuilder->andWhere('t.budget >= :min_price')
                ->setParameter('min_price', $data->min_price);
        }

        if ($data->max_price) {
            $queryBuilder->andWhere('t.budget <= :max_price')
                ->setParameter('max_price', $data->max_price);
        }

        if ($data->min_age) {
            $queryBuilder->andWhere('t.age >= :min_age')
                ->setParameter('min_age', $data->min_age);
        }

        if ($data->max_age) {
            $queryBuilder->andWhere('t.age <= :max_age')
                ->setParameter('max_age', $data->max_age);
        }

        if ($data->city) {
            $queryBuilder->andWhere('t.city = :city')
                ->setParameter('city', $data->city);
        }

        return $this->paginator->paginate(
            $queryBuilder,
            $data->page,
            $limit
        );
    }
}
