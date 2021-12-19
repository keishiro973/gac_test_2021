<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\Vehicle;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function totalTeBetween(DateTime $startDate = null, DateTime $endDate = null): array
    {
        $query = $this->createQueryBuilder('e')
            ->select('SUM(e.valueTe) as totalTe');

        return $query->getQuery()->getResult();
    }

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return int|mixed|string
     */
    public function totalTiBetween(DateTime $startDate = null, DateTime $endDate = null): array
    {
        $query = $this->createQueryBuilder('e')
            ->select('SUM(e.valueTi) as totalTi');
        $this->whereStartDate($startDate, $query);

        $this->whereEndDate($endDate, $query);

        return $query->getQuery()->getResult();
    }

    public function sumByCategory(DateTime $startDate = null, DateTime $endDate = null): array
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.category, SUM(e.valueTe) as totalTe')
            ->groupBy('e.category');
        $this->whereStartDate($startDate, $query);

        $this->whereEndDate($endDate, $query);

        return $query->getQuery()->getResult();
    }

    public function top10Vehicle(DateTime $startDate = null, DateTime $endDate = null): array
    {
        $query = $this->createQueryBuilder('e')
            ->select('v.plateNumber, SUM(e.valueTe) as totalTe')
            ->innerJoin('e.vehicle', 'v', Join::ON)
            ->groupBy('v.plateNumber')
            ->orderBy('totalTe', 'DESC')
            ->setMaxResults('10');;
        $this->whereStartDate($startDate, $query);

        $this->whereEndDate($endDate, $query);

        return $query->getQuery()->getResult();
    }

    public function last50Expense(Vehicle $vehicle)
    {
        $query = $this->createQueryBuilder('e')
            ->innerJoin('e.vehicle', 'v', Join::ON)
            ->andWhere('e.vehicle = :vehicle')
            ->setMaxResults('50')
            ->orderBy('e.issuedOn', 'DESC')
            ->setParameters([
                'vehicle' => $vehicle
            ]);
    }
    // /**
    //  * @return Expense[] Returns an array of Expense objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Expense
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param DateTime|null $startDate
     * @param QueryBuilder $query
     * @return void
     */
    public function whereStartDate(?DateTime $startDate, QueryBuilder $query): void
    {
        if ($startDate) {
            $query
                ->andWhere('e.issuedOn >= :startDate')
                ->setParameter('startDate', $startDate);
        }
    }

    /**
     * @param DateTime|null $endDate
     * @param QueryBuilder $query
     * @return void
     */
    public function whereEndDate(?DateTime $endDate, QueryBuilder $query): void
    {
        if ($endDate) {
            $query
                ->andWhere('e.issuedOn <= :endDate')
                ->setParameter('endDate', $endDate);
        }
    }
}
