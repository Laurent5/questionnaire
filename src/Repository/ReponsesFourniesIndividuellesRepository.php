<?php

namespace App\Repository;

use App\Entity\ReponsesFourniesIndividuelles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsesFourniesIndividuelles|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsesFourniesIndividuelles|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsesFourniesIndividuelles[]    findAll()
 * @method ReponsesFourniesIndividuelles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsesFourniesIndividuellesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsesFourniesIndividuelles::class);
    }
}
