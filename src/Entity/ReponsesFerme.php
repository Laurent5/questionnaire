<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesFermeRepository")
 */
class ReponsesFerme extends Reponses
{
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuestionPrerequis", mappedBy="reponse", orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $multiple;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $texte;


    /**
     * @var ReponsesFerme
     * @ORM\OneToMany(targetEntity="ReponsesFourniesIndividuellesFerme", mappedBy="reponsesFerme")
     */
    private $reponsesFournies;



    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->reponsesFournies = new ArrayCollection();
    }


    public function getMultiple(): ?bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return Collection|QuestionPrerequis[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(QuestionPrerequis $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setReponse($this);
        }

        return $this;
    }

    public function removeQuestion(QuestionPrerequis $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getReponse() === $this) {
                $question->setReponse(null);
            }
        }

        return $this;
    }


    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * @return Collection|ReponsesFourniesIndividuellesFerme[]
     */
    public function getReponsesFournies(): Collection
    {
        $retour = new ArrayCollection();

        /** @var ReponsesFourniesIndividuellesFerme $rfi */
        foreach ($this->reponsesFournies as $rfi){
            if($rfi->getQuestionnaire()->getJoindreALAnalyse()){
                $retour->add($rfi);
            }
        }

        return $retour;
    }

    public function addReponsesFourny(ReponsesFourniesIndividuellesFerme $reponsesFourny): self
    {
        if (!$this->reponsesFournies->contains($reponsesFourny)) {
            $this->reponsesFournies[] = $reponsesFourny;
            $reponsesFourny->setReponsesFerme($this);
        }

        return $this;
    }

    public function removeReponsesFourny(ReponsesFourniesIndividuellesFerme $reponsesFourny): self
    {
        if ($this->reponsesFournies->contains($reponsesFourny)) {
            $this->reponsesFournies->removeElement($reponsesFourny);
            // set the owning side to null (unless already changed)
            if ($reponsesFourny->getReponsesFerme() === $this) {
                $reponsesFourny->setReponsesFerme(null);
            }
        }

        return $this;
    }
}
