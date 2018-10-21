<?php

namespace App\Repository;

use App\Entity\Fin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Fin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fin[]    findAll()
 * @method Fin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Fin::class);
    }

//    /**
//     * @return Fin[] Returns an array of Fin objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fin
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
