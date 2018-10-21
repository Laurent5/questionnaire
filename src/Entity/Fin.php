<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FinRepository")
 * @UniqueEntity("ordre",message="Attention il y a déjà une fin ayant cette ordre (Référence unique)")
 */
class Fin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @var ReponsesFerme
     * @ORM\ManyToOne(targetEntity="ReponsesFerme")
     */
    private $filtre;

    public function getId()
    {
        return $this->id;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

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

    public function getFiltre(): ?ReponsesFerme
    {
        return $this->filtre;
    }

    public function setFiltre(?ReponsesFerme $filtre): self
    {
        $this->filtre = $filtre;

        return $this;
    }
}
