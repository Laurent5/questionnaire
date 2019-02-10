<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorisationRepository")
 */
class Categorisation
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
    private $categorie;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="ReponsesFourniesIndividuellesOuverte", mappedBy="categories")
     */
    private $reponsesFournies;

    /**
     * @var Question
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="categories")
     */
    private $question;

    public function __construct()
    {
        $this->reponsesFournies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

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

    /**
     * @return Collection|ReponsesFourniesIndividuellesOuverte[]
     */
    public function getReponsesFournies(): Collection
    {
        return $this->reponsesFournies;
    }

    public function addReponsesFournies(ReponsesFourniesIndividuellesOuverte $reponsesFournies): self
    {
        if (!$this->reponsesFournies->contains($reponsesFournies)) {
            $this->reponsesFournies[] = $reponsesFournies;
            $reponsesFournies->addCategory($this);
        }

        return $this;
    }

    public function removeReponsesFournies(ReponsesFourniesIndividuellesOuverte $reponsesFournies): self
    {
        if ($this->reponsesFournies->contains($reponsesFournies)) {
            $this->reponsesFournies->removeElement($reponsesFournies);
            $reponsesFournies->removeCategory($this);
        }

        return $this;
    }

    public function addReponsesFourny(ReponsesFourniesIndividuellesOuverte $reponsesFourny): self
    {
        if (!$this->reponsesFournies->contains($reponsesFourny)) {
            $this->reponsesFournies[] = $reponsesFourny;
            $reponsesFourny->addCategory($this);
        }

        return $this;
    }

    public function removeReponsesFourny(ReponsesFourniesIndividuellesOuverte $reponsesFourny): self
    {
        if ($this->reponsesFournies->contains($reponsesFourny)) {
            $this->reponsesFournies->removeElement($reponsesFourny);
            $reponsesFourny->removeCategory($this);
        }

        return $this;
    }
}
