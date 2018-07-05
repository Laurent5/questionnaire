<?php

namespace App\Repository;

use App\Entity\ReponsesFerme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesFerme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesFerme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesFerme[]    findAll()
 * @method ReponsesFerme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesFermeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesFerme::class);
    }

//    /**
//     * @return ReponsesFerme[] Returns an array of ReponsesFerme objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReponsesFerme
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
