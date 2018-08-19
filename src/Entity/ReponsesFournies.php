<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesFourniesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ReponsesFournies
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $repondant_token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $questionnaire_id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ReponsesFourniesIndividuelles", mappedBy="questionnaire", cascade={ "persist", "remove"}, orphanRemoval=true)
     */
    private $reponses;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRepondantToken(): ?string
    {
        return $this->repondant_token;
    }

    public function setRepondantToken(string $repondant_token): self
    {
        $this->repondant_token = $repondant_token;

        return $this;
    }

    public function getQuestionnaireId(): ?string
    {
        return $this->questionnaire_id;
    }

    public function setQuestionnaireId(string $questionnaire_id): self
    {
        $this->questionnaire_id = $questionnaire_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|ReponsesFourniesIndividuelles[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(ReponsesFourniesIndividuelles $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeReponse(ReponsesFourniesIndividuelles $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestionnaire() === $this) {
                $reponse->setQuestionnaire(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersit(){
        if(null==$this->getQuestionnaireId()){
            $this->setCreatedAt(new \DateTime());
        }

        $this->setQuestionnaireId(uniqid());
    }
}
