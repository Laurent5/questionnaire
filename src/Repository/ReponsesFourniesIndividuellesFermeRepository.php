<?php

namespace App\Repository;

use App\Entity\ReponsesFourniesIndividuellesFerme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesFourniesIndividuellesFerme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesFourniesIndividuellesFerme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesFourniesIndividuellesFerme[]    findAll()
 * @method ReponsesFourniesIndividuellesFerme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesFourniesIndividuellesFermeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesFourniesIndividuellesFerme::class);
    }

//    /**
//     * @return ReponsesFourniesIndividuellesFerme[] Returns an array of ReponsesFourniesIndividuellesFerme objects
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
    public function findOneBySomeField($value): ?ReponsesFourniesIndividuellesFerme
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
