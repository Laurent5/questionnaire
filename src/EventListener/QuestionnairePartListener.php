<?php
/**
 * Created by PhpStorm.
 * User: laurent5
 * Date: 17/06/18
 * Time: 16:43
 */

namespace App\EventListener;


use App\Entity\Question;
use App\Entity\Reponses;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesFourniesIndividuellesFerme;
use App\Entity\ReponsesFourniesIndividuellesOuverte;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class QuestionnairePartListener implements EventSubscriberInterface
{
    /** @var ManagerRegistry */
    private $manager;

    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'onSubmit'
        );
    }

    public function onSubmit(FormEvent $event){
        /**
         * @var integer $key
         * @var Reponses $value
         */
        $reponses = new ReponsesFournies();
        foreach ($event->getData() as $key => $value) {
            $this->traitReponse($key,$value,$reponses);
        }

        foreach ($event->getForm()->getExtraData() as $key => $value){
            $this->traitReponse($key,$value,$reponses);
        }

        dump($reponses);

        $event->setData($reponses);
    }

    private function traitReponse($key,$value,ReponsesFournies $reponses){
        /** @var Question $question */
        $question = $this->manager->getRepository(Question::class)->find($key);

        if(get_class($question->getReponses()->first()) == ReponsesFerme::class){
            /** @var ReponsesFerme $reponse */
            $reponse = $this->manager->getRepository(ReponsesFerme::class)->find($value);
            $reponseFournie = new ReponsesFourniesIndividuellesFerme();
            $reponseFournie->setReponsesFerme($reponse)->setQuestions($question);
            $reponses->addReponse($reponseFournie);
        }else{
            $reponseFournie = new ReponsesFourniesIndividuellesOuverte();
            $reponseFournie->setValeur($value)->setQuestions($question);
            $reponses->addReponse($reponseFournie);
        }
    }
}