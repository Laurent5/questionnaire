<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesFourniesIndividuellesOuverteRepository")
 */
class ReponsesFourniesIndividuellesOuverte extends ReponsesFourniesIndividuelles
{
    /**
     * @ORM\Column(type="text")
     */
    private $valeur;

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }
}
