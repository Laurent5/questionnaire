<?php

namespace App\Repository;

use App\Entity\QuestionPrerequis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method QuestionPrerequis|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionPrerequis|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionPrerequis[]    findAll()
 * @method QuestionPrerequis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionPrerequisRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QuestionPrerequis::class);
    }

//    /**
//     * @return QuestionPrerequis[] Returns an array of QuestionPrerequis objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuestionPrerequis
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
