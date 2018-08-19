<?php
/**
 * Created by PhpStorm.
 * User: laurent5
 * Date: 14/05/18
 * Time: 21:48
 */

namespace App\Service;


use App\Entity\Question;
use App\Entity\QuestionPrerequis;
use App\Entity\Reponses;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\EventListener\QuestionnairePartListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class FormQuestionnaireFactory
{
    /** @var ManagerRegistry  */
    private $register;
    /** @var FormFactory  */
    private $formFactory;
    /** @var ManagerRegistry */
    private $manager;

    public function __construct(ManagerRegistry $register, FormFactoryInterface $formFactory, ManagerRegistry $manager)
    {
        $this->register = $register;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    /**
     * @param Thematique $thematique
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getFormFor(Thematique $thematique){

        $form = $this->formFactory->createBuilder(FormType::class,null,array(
            'allow_extra_fields' => true
        ));
        $form->addEventSubscriber(
            new QuestionnairePartListener($this->manager)
        );

        $form = $form->getForm();


        $questions = $this->orderAndFilter($thematique->getQuestions());

        /** @var Question $question */
        foreach ($questions as $question){
            $form = $this->createFormFromQuestion($question,$form);
        }

        $form->add("Suivant",SubmitType::class);

        return $form;

    }

    private function orderAndFilter(PersistentCollection $questions){
        /** @var Question $question */
        foreach ($questions as $key => $question){
            if($question instanceof Question && $question->getOrdre() === null){
                $questions->remove($key);
            }
        }

        $questions = $questions->toArray();
        usort($questions , array(Question::class,"order"));

        return $questions;
    }

    /**
     * @param Question $question
     * @param FormInterface|null $form
     * @return FormInterface
     */
    public function createFormFromQuestion(Question $question, FormInterface $form = null){
        if($form === null)
        {
            $form = $this->formFactory->create();
        }

        /** @var Reponses $question */
        $reponse = $question->getReponses()->first();
        $optionArray['label'] = $question->getQuestion();
        if($question->getAide()!==null){
            $optionArray['help'] = $question->getAide();
        }

        switch (get_class($reponse)){
            case ReponsesFerme::class :
                /** @var ReponsesFerme $reponse */
                $formType = ChoiceType::class;
                $values = array();
                $addRoute = [];
                $expensed = false;
                /** @var ReponsesFerme $reponse */
                foreach ($question->getReponses() as $reponse) {
                    $values[$reponse->getTexte()] = $reponse->getId();
                    if ($reponse->getQuestions()->count() > 0) {
                        $addRoute["data-route-".$reponse->getId()] = true;
                        $expensed = true;
                    }
                }
                $addRoute["data-expensed"] = $expensed;
                $optionArray['choices'] = $values;
                $optionArray['expanded'] = true;
                $optionArray['multiple'] = $reponse->getMultiple();
                $optionArray['attr'] = $addRoute;

                break;

            case ReponsesOuverte::class :
                $formType = null;
                $constraints = null;
                /** @var ReponsesOuverte $reponse */
                switch ($reponse->getType()->getType()){
                    case 'Nombre entier' :
                        $formType = IntegerType::class;
                        $optionArray['constraints'] = array(
                            new NotBlank(),
                            new NotNull(),
                            new Type(array(
                                'message' => "Ce nombre doit-être un entier",
                                'type' => 'integer'
                            )),
                            new GreaterThan(array(
                                'value' => 0,
                                'message' => 'Ce nombre doit-être possitif'
                            ))
                        );
                        break;
                    case 'Texte' :
                        $formType = TextareaType::class;
                        if($reponse->getObligatoire()) {
                            $optionArray['constraints'] = array(
                                new NotBlank(),
                                new NotNull(),
                                new Length(array(
                                    'min' => 2,
                                    'minMessage' => 'Merci d\'entrer au moins {{ limit }} caractères'
                                ))
                            );
                        }
                        break;
                }


                break;
        }

        $form->add($question->getId(),$formType,$optionArray);

        return $form;
    }

    public function createFormFromReponse(ReponsesFerme $reponse, ReponsesFournies $reponsesFournies){
        $form = $this->formFactory->create();

        /** @var QuestionPrerequis $question */
        foreach ($reponse->getQuestions() as $questionPrerequis){
            /** @var Question $question */
            $question = $questionPrerequis->getQuestion();
            $ok = true;
            $nombreTotalOptionnel = 0;
            $nombreOptionnelOk = 0;
            /** @var QuestionPrerequis $reponsePreRecquise */
            foreach ($question->getReponsePreRequise() as $reponsePreRecquise){
                /** @var Reponses $reponse */
                $reponsePR= $reponsePreRecquise->getReponse();
                if($reponsePR->getId() != $reponse->getId() && !$reponsesFournies->getReponses()->contains($reponsePR) && !$reponsePreRecquise->getOptionnel()){
                    $ok = false;
                }

                if($reponsePreRecquise->getOptionnel()){
                    $nombreTotalOptionnel += 1;

                    if($reponsesFournies->getReponses()->contains($reponsePR) || $reponsePR->getId() == $reponse->getId()){
                        $nombreOptionnelOk += 1;
                    }
                }

            }

            if($nombreTotalOptionnel > 1 && $nombreOptionnelOk <= 0){
                $ok = false;
            }


            if($ok){
                $form = $this->createFormFromQuestion($question,$form);
            }
        }

        return $form;
    }

}