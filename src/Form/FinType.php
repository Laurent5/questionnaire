<?php

namespace App\Form;

use App\Entity\Fin;
use App\Entity\Question;
use App\Entity\ReponsesFerme;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class FinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question_pre_requis',EntityType::class,array(
                'class' => Question::class,
                'mapped' => false,
                'label' => 'Pour quelle question rajouter une sous-question ?',
                'attr' => array('data-selected' => true),
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

            ->add('filtre',EntityType::class,array(
                'label' => 'Pour quelle réponse créer cette sous-question ?',
                'attr' => array('data-reponses' => true),
                'class' => ReponsesFerme::class,
                'choice_label' => 'texte'
            ))

            ->addEventListener(FormEvents::POST_SET_DATA, array($this,'onPostSetData'))

            ->add('ordre')
            ->add('texte')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fin::class,
        ]);
    }
    public function onPostSetData(FormEvent $formEvent){
        /** @var null|Fin $data */
        $data = $formEvent->getData();

        if($data!==null){
            $question = $data->getFiltre()->getQuestion();
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
