<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuestionPrerequis;
use App\Entity\ReponsesFerme;
use App\Entity\Thematique;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class FiltreType extends AbstractType
{
    /** @var ManagerRegistry  */
    protected $manager;

    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question_pre_requis',EntityType::class,array(
                'class' => Question::class,
                'mapped' => false,
                'attr' => array('data-selected' => true),
                'label' => 'Sur quelle question rajouter un filtre ?',
                'placeholder' => '',
                'constraints' => array(new NotBlank(), new NotNull()),
                'query_builder' => function (EntityRepository $entityRepository){
                    $qb = $entityRepository->createQueryBuilder('q');
                    return $qb
                        ->leftJoin('q.reponses','r')
                        ->where($qb->expr()->isInstanceOf('r',ReponsesFerme::class))
                        ;
                },
                'choice_label' => function(Question $question){
                    return $question->getOrdre().") ".$question->getQuestion();
                }

            ))
            ->add('reponse',EntityType::class,array(
                'label' => 'Pour quelle réponse ajouter un filtre ?',
                'attr' => array('data-reponses' => true),
                'class' => ReponsesFerme::class,
                'choice_label' => 'texte'
            ))
            ->addEventListener(FormEvents::POST_SUBMIT,array($this,'onPostSubmit'))
            ->addEventListener(FormEvents::POST_SET_DATA, array($this,'onPostSetData'))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionPrerequis::class,
        ]);
    }

    public function onPostSubmit(FormEvent $formEvent)
    {
        /** @var QuestionPrerequis $data */
        $data = $formEvent->getData();
        $reponse = null;

        if($data->getOptionnel() === null)
        {
            $data->setOptionnel(true);
        }
    }

    public function onPostSetData(FormEvent $formEvent){
        /** @var null|QuestionPrerequis $data */
        $data = $formEvent->getData();



        if($data!==null){
            $question = $data->getReponse()->getQuestion()->getId();
            $question = $this->manager->getRepository(Question::class)->find($question);
            $this->setDataAt($formEvent->getForm(), 'question_pre_requis', 'data', $question);
        }
    }

    protected function setDataAt($form, $fieldName, $optionName, $optionValue){
        $typeForm = $form->get($fieldName)->getConfig()->getType()->getInnerType();
        $options = $form->get($fieldName)->getConfig()->getOptions();
        $options[$optionName] = $optionValue;
        $form->add($fieldName,get_class($typeForm),$options);
    }
}
