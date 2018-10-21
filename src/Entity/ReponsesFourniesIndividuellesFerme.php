<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesFourniesIndividuellesFermeRepository")
 */
class ReponsesFourniesIndividuellesFerme extends ReponsesFourniesIndividuelles
{
    /**
     * @var ReponsesFerme
     * @ORM\ManyToOne(targetEntity="ReponsesFerme", cascade={"remove"})
     */
   private $reponsesFerme;

   public function getReponsesFerme(): ?ReponsesFerme
   {
       return $this->reponsesFerme;
   }


   public function setReponsesFerme(?ReponsesFerme $reponsesFerme): self
   {
       $this->reponsesFerme = $reponsesFerme;
       return $this;
   }

   public function getValeur(): ?string
   {
       return $this->getReponsesFerme()->getTexte();
   }
}
