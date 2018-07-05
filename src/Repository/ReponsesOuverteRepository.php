<?php

namespace App\Repository;

use App\Entity\ReponsesOuverte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesOuverte|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesOuverte|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesOuverte[]    findAll()
 * @method ReponsesOuverte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesOuverteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesOuverte::class);
    }

//    /**
//     * @return ReponsesOuverte[] Returns an array of ReponsesOuverte objects
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
    public function findOneBySomeField($value): ?ReponsesOuverte
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
