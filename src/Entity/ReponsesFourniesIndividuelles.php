<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesFourniesIndividuellesRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"ouverte" = "ReponsesFourniesIndividuellesOuverte", "ferme" = "ReponsesFourniesIndividuellesFerme"})
 */
abstract class ReponsesFourniesIndividuelles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Reponses
     * @ORM\ManyToOne(targetEntity="Question")
     */
    private $questions;

    /**
     * @var ReponsesFournies
     * @ORM\ManyToOne(targetEntity="ReponsesFournies", inversedBy="reponses")
     */
    private $questionnaire;


    public function getId()
    {
        return $this->id;
    }

    public function getQuestions(): ?Question
    {
        return $this->questions;
    }

    public function setQuestions(?Question $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function getQuestionnaire(): ?ReponsesFournies
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?ReponsesFournies $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

}
