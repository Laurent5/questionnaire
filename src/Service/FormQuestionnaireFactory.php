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
use App\Entity\ReponsesFourniesIndividuellesFerme;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\EventListener\QuestionnairePartListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\PersistentCollection;
use PhpParser\Node\Stmt\Throw_;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Doctrine\Common\Util\ClassUtils;

class FormQuestionnaireFactory
{
    /** @var ManagerRegistry */
    private $register;
    /** @var FormFactory */
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
    public function getFormFor(Thematique $thematique, ReponsesFournies $questionnaire = null)
    {

        $form = $this->formFactory->createBuilder(FormType::class, null, array(
            'allow_extra_fields' => true
        ));
        $form->addEventSubscriber(
            new QuestionnairePartListener($this->manager)
        );

        $form = $form->getForm();


        $questions = $this->orderAndFilter($thematique->getQuestions(), $questionnaire);

        /** @var Question $question */
        foreach ($questions as $question) {
            $retour = $this->createFormFromQuestion($question, $form);
            if ($retour != null) {
                $form = $retour;
            }


        }

        $form->add("Suivant", SubmitType::class);

        return $form;

    }

    private function orderAndFilter(PersistentCollection $questions, ReponsesFournies $questionnaire = null)
    {

        $questions = $questions->toArray();
        usort($questions, array(Question::class, "order"));
        $questions = new ArrayCollection($questions);
        $oldKey = -1;

        /** @var Question $question */
        foreach ($questions as $key => $question) {
            if ($question instanceof Question && $question->getOrdre() === null) {
                $questions->remove($key);
            }

            //Est-ce une sous-question ou une question avec filtre ?
            if ($question->getReponsePreRequise()->count() > 0 && !$this->preRequisOk($question,$questionnaire) ){
                $questions->remove($key);

            }

            if($oldKey == $question->getOrdre()){
                $questions->remove($key);
            }

            $oldKey = $question->getOrdre();
        }

        return $questions;
    }


    /**
     * @param Question $question
     * @param FormInterface|null $form
     * @return FormInterface|null
     */
    public function createFormFromQuestion(Question $question, FormInterface $form = null)
    {

        if ($form === null) {
            $form = $this->formFactory->create();
        }


        /** @var Reponses $question */
        $reponse = $question->getReponses()->first();
        $optionArray['label'] = $question->getQuestion();
        if ($question->getAide() !== null) {
            $optionArray['help'] = $question->getAide();
        }

        switch (ClassUtils::getClass($reponse)) {
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
                        $affiche = false;
                        /** @var QuestionPrerequis $questionInduite */
                        foreach ($reponse->getQuestions() as $questionInduite){
                            if($questionInduite->getQuestion()->getThematique()->getId() == $question->getThematique()->getId()){
                                $affiche = true;
                                break;
                            }
                        }

                        if($affiche){
                            $addRoute["data-route-" . $reponse->getId()] = true;
                            $expensed = true;
                        }

                    }
                }
                $addRoute["data-expensed"] = $expensed;
                $optionArray['choices'] = $values;
                $optionArray['expanded'] = true;
                $optionArray['multiple'] = $reponse->getMultiple();
                $addRoute["data-multiple"] = $reponse->getMultiple();
                if ($reponse->getMultiple()) {
                    $optionArray['required'] = true;
                    $optionArray['constraints'] = array(
                        new NotBlank(),
                        new NotNull(),
                        new Count(array(
                            'min' => 1
                        ))
                    );
                }
                $optionArray['attr'] = $addRoute;

                break;

            case ReponsesOuverte::class :
                $formType = null;
                $constraints = null;
                /** @var ReponsesOuverte $reponse */
                switch ($reponse->getType()->getType()) {
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
                        if ($reponse->getObligatoire()) {
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

            default:
                Throw new \LogicException("Ce type de réponse n'est pas pris en charge : " . ClassUtils::getClass($reponse));
        }

        $form->add($question->getId(), $formType, $optionArray);

        return $form;
    }

    public function createFormFromReponse(ReponsesFerme $reponse, ReponsesFournies $reponsesFournies)
    {
        $form = $this->formFactory->create();

        $questionnaireModifie = clone $reponsesFournies;

        $ri = new ReponsesFourniesIndividuellesFerme();
        $ri->setQuestions($reponse->getQuestion());
        $ri->setQuestionnaire($questionnaireModifie);
        $ri->setReponsesFerme($reponse);

        $questionnaireModifie->addReponse($ri);

        /** @var QuestionPrerequis $question */
        foreach ($reponse->getQuestions() as $question){
            if ($this->preRequisOk($question->getQuestion(),$questionnaireModifie) && $question->getQuestion()->getThematique()->getId() == $reponse->getQuestion()->getThematique()->getId()) {
                $form = $this->createFormFromQuestion($question->getQuestion(), $form);
            }
        }


        unset($ri);
        unset($questionnaireModifie);



        return $form;
    }

    /**
     * @param Question $question
     * @param ReponsesFournies $questionnaire
     * @return bool
     */
    private function preRequisOk(Question $question, ReponsesFournies $questionnaire)
    {
        if($questionnaire == null || ClassUtils::getClass($questionnaire)!=ReponsesFournies::class){
            return false;
        }

        $ok = true;
        $nombreTotalOptionnel = 0;
        $nombreOptionnelOk = 0;


        /** @var QuestionPrerequis $reponsePreRecquise */
        foreach ($question->getReponsePreRequise() as $reponsePreRecquise) {
            /** @var Reponses $reponse */
            $reponsePR = $reponsePreRecquise->getReponse();

            if ($reponsePreRecquise->getOptionnel()) {
                $nombreTotalOptionnel += 1;


                /** @var ReponsesFourniesIndividuelles $reponsesFourniesIndividuelles */
                foreach ($questionnaire->getReponses() as $reponsesFourniesIndividuelles){
                    if(ClassUtils::getClass($reponsesFourniesIndividuelles)==ReponsesFourniesIndividuellesFerme::class && $reponsesFourniesIndividuelles->getReponsesFerme()->getId() == $reponsePR->getId()){
                        $nombreOptionnelOk += 1;
                    }
                }
            }else{
                $ok = false;
                foreach ($questionnaire->getReponses() as $reponsesFourniesIndividuelles){
                    if(ClassUtils::getClass($reponsesFourniesIndividuelles) == ReponsesFourniesIndividuellesFerme::class && $reponsesFourniesIndividuelles->getReponsesFerme()->getId() == $reponsePR->getId()){
                        $ok = true;
                        break;
                    }
                }
            }

        }

        if ($nombreTotalOptionnel > 1 && $nombreOptionnelOk <= 0) {
            $ok = false;
        }

        return $ok;

    }

}