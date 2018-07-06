<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionPrerequisRepository")
 * @UniqueEntity(fields={"reponse","question"})
 */
class QuestionPrerequis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ReponsesFerme
     * @ORM\ManyToOne(targetEntity="ReponsesFerme", inversedBy="questions", cascade={"persist"})
     */
    private $reponse;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="reponsePreRequise", cascade={"persist"})
     */
    private $question;

    /**
     * @ORM\Column(type="boolean")
     */
    private $optionnel;

    

    public function getId()
    {
        return $this->id;
    }

    public function getReponse(): ?ReponsesFerme
    {
        return $this->reponse;
    }

    public function setReponse(?ReponsesFerme $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getOptionnel(): ?bool
    {
        return $this->optionnel;
    }

    public function setOptionnel(bool $optionnel): self
    {
        $this->optionnel = $optionnel;

        return $this;
    }

}
