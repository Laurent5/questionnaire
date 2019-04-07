<?php

namespace App\Repository;

use App\Entity\ReponsesFournies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesFournies|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesFournies|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesFournies[]    findAll()
 * @method ReponsesFournies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesFourniesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesFournies::class);
    }

    public function getAllActives(){
        return $this
            ->createQueryBuilder('que')
            ->select(array("que","rfi"))
            ->where("que.joindreALAnalyse = :true")
            ->leftJoin("que.reponses","rfi")
            ->setParameter("true",true)
            ->getQuery()
            ->execute()
            ;
    }

//    /**
//     * @return ReponsesFournies[] Returns an array of ReponsesFournies objects
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
    public function findOneBySomeField($value): ?ReponsesFournies
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
