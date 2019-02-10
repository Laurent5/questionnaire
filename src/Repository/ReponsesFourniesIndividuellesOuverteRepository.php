<?php

namespace App\Repository;

use App\Entity\ReponsesFourniesIndividuellesOuverte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesFourniesIndividuellesOuverte|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesFourniesIndividuellesOuverte|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesFourniesIndividuellesOuverte[]    findAll()
 * @method ReponsesFourniesIndividuellesOuverte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesFourniesIndividuellesOuverteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesFourniesIndividuellesOuverte::class);
    }

//    /**
//     * @return ReponsesFourniesIndividuellesOuverte[] Returns an array of ReponsesFourniesIndividuellesOuverte objects
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
    public function findOneBySomeField($value): ?ReponsesFourniesIndividuellesOuverte
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getReponseWithoutCategories(){
        return $this->createQueryBuilder("q")
            ->leftJoin('q.categories','c')
            ->leftJoin('q.questions','qq')
            ->leftJoin('qq.categories','cat')
            ->where('c IS NULL')
            ->andWhere('cat IS NOT NULL')
            ->getQuery()
            ->execute()
            ;
    }
}
