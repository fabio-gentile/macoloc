<?php

namespace App\Repository;

use App\Data\Admin\SearchData;
use App\Data\SearchHousingData;
use App\Entity\Housing;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Housing>
 */
class HousingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Housing::class);
    }

//    /**
//     * @return Housing[] Returns an array of Housing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Housing
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Find housings by search data
     * @param SearchData $searchData
     * @param int $limit
     * @return PaginationInterface
     */
    public function findSearchByUser(SearchData $searchData, int $limit = 7): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('h');

        if ($searchData->q) {
            $query = strtolower($searchData->q);
            $queryBuilder
                ->join('h.user', 'u')
                ->andWhere('LOWER(u.firstname) LIKE :query')
                ->orWhere('LOWER(u.lastname) LIKE :query')
                ->setParameter('query', "%{$query}%");
        }

        $queryBuilder->orderBy('h.createdAt', 'DESC');

        return $this->paginator->paginate(
            $queryBuilder,
            $searchData->page,
            $limit
        );
    }

    /**
     * Find latest housings
     * @param DateTime $yesterday
     * @return Housing[]|null
     */
    public function findLatest(DateTime $yesterday = new DateTime('-1 day')): ?array
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.createdAt', 'DESC')
            ->where('h.createdAt BETWEEN :yesterday AND :today')
            ->setParameter('yesterday', $yesterday)
            ->setParameter('today', new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * Find housings by filters
     * @param SearchHousingData $data
     * @param string|null $city
     * @param int $limit
     * @return PaginationInterface
     */
    public function findSearch(SearchHousingData $data, string $city = null, int $limit = 7): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('h');

        if ($data->house_type) {
            $queryBuilder->andWhere('h.type IN (:house_type)')
                ->setParameter('house_type', $data->house_type);
        }

        if ($data->commodity) {
            $commodities = is_array($data->commodity) ? $data->commodity : [$data->commodity];
            foreach ($commodities as $index => $commodity) {
                $paramName = "commodity_$index";
                $queryBuilder->andWhere("JSONB_CONTAINS(h.commodity, :$paramName) = true")
                    ->setParameter($paramName, json_encode([$commodity]));
            }
        }

        if ($data->numberOfRooms) {
            if (is_array($data->numberOfRooms) && in_array(5, $data->numberOfRooms)) {
                $queryBuilder->andWhere('h.numberOfRooms >= :numberOfRooms')
                    ->setParameter('numberOfRooms', 5);
            } else {
                $queryBuilder->andWhere('h.numberOfRooms IN (:numberOfRooms)')
                    ->setParameter('numberOfRooms', $data->numberOfRooms);
            }
        }

        if ($city) {
            $queryBuilder->andWhere('h.city = :city')
                ->setParameter('city', $city);
        }

        if ($data->disponibility) {
            $queryBuilder->andWhere('h.avaibleAt <= :disponibility')
                ->setParameter('disponibility', new DateTime());
        }

        if ($data->min_price) {
            $queryBuilder->andWhere('h.price >= :min_price')
                ->setParameter('min_price', $data->min_price);
        }

        if ($data->max_price) {
            $queryBuilder->andWhere('h.price <= :max_price')
                ->setParameter('max_price', $data->max_price);
        }

        if ($data->postcode) {
            $queryBuilder->andWhere('h.postcode = :postcode')
                ->setParameter('postcode', $data->postcode);
        }

        if ($data->city) {
            $queryBuilder->andWhere('h.city = :city')
                ->setParameter('city', $data->city);
        }

        return $this->paginator->paginate(
            $queryBuilder,
            $data->page,
            $limit
        );
    }

}
