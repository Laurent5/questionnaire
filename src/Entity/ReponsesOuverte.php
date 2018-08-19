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

    /**
     * @ORM\Column(type="boolean")
     */
    private $obligatoire;


    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getObligatoire(): ?bool
    {
        return $this->obligatoire;
    }

    public function setObligatoire(bool $obligatoire): self
    {
        $this->obligatoire = $obligatoire;

        return $this;
    }
}
