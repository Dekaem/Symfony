<?php

namespace App\Repository;

use App\Entity\TableRonde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableRonde|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableRonde|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableRonde[]    findAll()
 * @method TableRonde[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRondeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableRonde::class);
    }

    // /**
    //  * @return TableRonde[] Returns an array of TableRonde objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TableRonde
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
