<?php

namespace App\Repository;

use App\Entity\Categorisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Categorisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorisation[]    findAll()
 * @method Categorisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorisationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Categorisation::class);
    }

    public function getAllValidResponsesFrom(Categorisation $categorisation){
        return $this
            ->createQueryBuilder('c')
            ->select(array('c','rfi','que'))
            ->leftJoin('c.reponsesFournies','rfi')
            ->leftJoin('rfi.questionnaire','que')
            ->where('que.joindreALAnalyse = :true')
            ->andWhere('c.id = :id')
            ->setParameter('id',$categorisation->getId())
            ->setParameter('true',true)
            ->getQuery()
            ->getSingleResult()
            ;
    }

//    /**
//     * @return Categorisation[] Returns an array of Categorisation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Categorisation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
