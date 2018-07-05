<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponsesOuverteRepository")
 */
class ReponsesOuverte extends Reponses
{
    /**
     * @var Type
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="reponses")
     */
    private $type;


    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }
}
