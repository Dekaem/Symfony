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

    public function findByRound($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.round = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findById($value): ?TableRonde
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByTableNumberAndUserNomber($value): ?TableRonde
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.tableNumber = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByUserAndRound($user, $round): ?TableRonde
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.users = :user')
            ->andWhere('b.round = :round')
            ->setParameter('user', $user)
            ->setParameter('round', $round)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return TableRonde[] Returns an array of TableRonde objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TableRonde
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
