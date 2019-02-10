<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return mixed
     */
    public function getQuestionAvecReponses(){
        return $this->createQueryBuilder("q")
            ->select(array("q","rfi","qu","r"))
            ->distinct("q")
            ->innerJoin("q.reponsesFourniesIndividuelles","rfi")
            ->leftJoin("q.reponses","r")
            ->leftJoin("rfi.questionnaire","qu")
            ->where("rfi IS NOT NULL")
            ->orderBy("q.ordre","ASC")
            ->getQuery()
            ->execute()
            ;
    }


    /**
     * @return mixed
     */
    public function getQuestionAvecReponsesStats(){
        return $this->createQueryBuilder("q")
            ->select(array("q","rfi","qu","r"))
            ->distinct("q")
            ->innerJoin("q.reponsesFourniesIndividuelles","rfi")
            ->leftJoin("q.reponses","r")
            ->leftJoin("rfi.questionnaire","qu")
            ->where("rfi IS NOT NULL")
            ->orderBy("q.ordre","ASC")
            ->getQuery()
            ->execute()
            ;
    }

    public function getQuestions(){
        return $this->createQueryBuilder("q")
            ->select(array("q","rpr","c","r"))
            ->leftJoin("q.reponses","r")
            ->leftJoin("q.reponsePreRequise","rpr")
            ->leftJoin("q.categories","c")
            ->orderBy("q.ordre",'ASC')
            ->getQuery()
            ->execute()
            ;
    }
}
