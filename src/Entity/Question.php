<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @UniqueEntity("ordre",message="Attention une question ayant cette ordre (Référence unique)")
 */
class Question
{

    public const QUESTION_REPONSE_OUVERTE_SANS_FILTRE = "qrosf";
    public const QUESTION_REPONSE_OUVERTE_AVEC_FILTRE = "qroaf";
    public const QUESTION_REPONSE_FERME_SANS_FILTRE = "qrfsf";
    public const QUESTION_REPONSE_FERME_AVEC_FILTRE = "qrfav";
    public const SOUS_QUESTION_REPONSE_FERME = "sqrf";
    public const SOUS_QUESTION_REPONSE_OUVERTE = "sqro";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $question;

    /**
     * @ORM\Column(type="integer", nullable=true)     *
     * @Assert\Type("int",message="Ce nombre doit-être entier")
     */
    private $ordre;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Reponses", mappedBy="question", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $reponses;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ReponsesFourniesIndividuelles", mappedBy="questions",  cascade={"remove"})
     */
    private $reponsesFourniesIndividuelles;

    /**
     * @var Thematique
     * @ORM\ManyToOne(targetEntity="Thematique", inversedBy="questions")
     * @Assert\Valid()
     */
    private $thematique;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuestionPrerequis", mappedBy="question", cascade={"persist", "remove"})
     */
    private $reponsePreRequise;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $aide;


    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->reponsePreRequise = new ArrayCollection();
        $this->reponsesFourniesIndividuelles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
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

    /**
     * @return Collection|Reponses[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponses $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponses $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function getThematique(): ?Thematique
    {
        return $this->thematique;
    }

    public function setThematique(?Thematique $thematique): self
    {
        $this->thematique = $thematique;

        return $this;
    }

    static function order(Question $a, Question $b){
        return ($a->getOrdre() < $b->getOrdre()) ? -1 : 1;
    }

    /**
     * @return Collection|QuestionPrerequis[]
     */
    public function getReponsePreRequise(): Collection
    {
        return $this->reponsePreRequise;
    }

    public function addReponsePreRequise(QuestionPrerequis $reponsePreRequise): self
    {
        if (!$this->reponsePreRequise->contains($reponsePreRequise)) {
            $this->reponsePreRequise[] = $reponsePreRequise;
            $reponsePreRequise->setQuestion($this);
        }

        return $this;
    }

    public function removeReponsePreRequise(QuestionPrerequis $reponsePreRequise): self
    {
        if ($this->reponsePreRequise->contains($reponsePreRequise)) {
            $this->reponsePreRequise->removeElement($reponsePreRequise);
            // set the owning side to null (unless already changed)
            if ($reponsePreRequise->getQuestion() === $this) {
                $reponsePreRequise->setQuestion(null);
            }
        }

        return $this;
    }

    public function getAide(): ?string
    {
        return $this->aide;
    }

    public function setAide(?string $aide): self
    {
        $this->aide = $aide;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload){
        if($this->getReponsePreRequise()->count() !== 0){
            $tab = new ArrayCollection();

            /** @var QuestionPrerequis $reponse */
            foreach ($this->reponsePreRequise as $reponse){
                if($tab->contains($reponse->getReponse()->getId())){
                    $context->buildViolation("Vous avez sélectionné deux fois la même réponse")->atPath("reponsesPreRequise")->addViolation();
                }
                if($this->getReponsePreRequise()->count() > 1){
                    //On est dans le cas des réponses avec filtre

                    if($reponse->getReponse()->getQuestion()->getThematique()->getOrdre() > $this->thematique->getOrdre()){
                        $context->buildViolation("Les réponses doivent être dans une thématique située avant la question")->atPath("reponsesPreRequise")->addViolation();
                    }
                }

                $tab->add($reponse->getReponse()->getId());
            }
        }
    }

    /**
     * @return Collection|ReponsesFourniesIndividuelles[]
     */
    public function getReponsesFourniesIndividuelles(): Collection
    {
        return $this->reponsesFourniesIndividuelles;
    }

    public function addReponsesFourniesIndividuelle(ReponsesFourniesIndividuelles $reponsesFourniesIndividuelle): self
    {
        if (!$this->reponsesFourniesIndividuelles->contains($reponsesFourniesIndividuelle)) {
            $this->reponsesFourniesIndividuelles[] = $reponsesFourniesIndividuelle;
            $reponsesFourniesIndividuelle->setQuestions($this);
        }

        return $this;
    }

    public function removeReponsesFourniesIndividuelle(ReponsesFourniesIndividuelles $reponsesFourniesIndividuelle): self
    {
        if ($this->reponsesFourniesIndividuelles->contains($reponsesFourniesIndividuelle)) {
            $this->reponsesFourniesIndividuelles->removeElement($reponsesFourniesIndividuelle);
            // set the owning side to null (unless already changed)
            if ($reponsesFourniesIndividuelle->getQuestions() === $this) {
                $reponsesFourniesIndividuelle->setQuestions(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfQuestion(){

        // Il s'aggit d'une question avec réponses fermées
        if($this->getReponses() && !$this->getReponses()->isEmpty() && ClassUtils::getClass($this->getReponses()->first()) == ReponsesFerme::class){
            if($this->getReponsePreRequise()->count() == 0){
                return self::QUESTION_REPONSE_FERME_SANS_FILTRE;
            }else{
                if($question->getReponsePreRequise()->count() > 1){
                    return self::QUESTION_REPONSE_FERME_AVEC_FILTRE;
                }else{
                    /** @var QuestionPrerequis $questionPR */
                    $questionPR = $this->getReponsePreRequise()->first();
                    if($questionPR->getQuestion()->getThematique()->getId() == $this->getThematique()->getId()){
                        return self::SOUS_QUESTION_REPONSE_FERME;
                    }else{
                        return self::QUESTION_REPONSE_FERME_AVEC_FILTRE;
                    }
                }
            }
        }
        //Il s'aggit d'une question avec réponse ouverte
        elseif ($this->getReponses() && !$this->getReponses()->isEmpty() && ClassUtils::getClass($this->getReponses()->first()) == ReponsesOuverte::class) {
            if ($this->getReponsePreRequise()->count() == 0) {
                return self::QUESTION_REPONSE_OUVERTE_SANS_FILTRE;
            } else {
                if ($this->getReponsePreRequise()->count() > 1) {
                    return self::QUESTION_REPONSE_OUVERTE_AVEC_FILTRE;
                } else {
                    /** @var QuestionPrerequis $questionPR */
                    $questionPR = $this->getReponsePreRequise()->first();
                    if($questionPR->getQuestion()->getThematique()->getId() == $this->getThematique()->getId()){
                        return self::SOUS_QUESTION_REPONSE_OUVERTE;
                    }else{
                        return self::QUESTION_REPONSE_OUVERTE_AVEC_FILTRE;
                    }
                }
            }
        }

        throw new LogicException();
    }
    
}
