<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuestionPrerequis;
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

class SousQuestionFermeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question_pre_requis',EntityType::class,array(
                'class' => Question::class,
                'mapped' => false,
                'attr' => array('data-selected' => true),
                'label' => 'Pour quelle question rajouter une sous-question ?',
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
                'label' => 'Pour quelle réponse créer cette sous-question ?',
                'attr' => array('data-reponses' => true),
                'class' => ReponsesFerme::class,
                'choice_label' => 'texte'
            ))

            ->add('question',QuestionFermeForSousQuestionType::class, array(
                'label' => false,
                'constraints' => array(new Valid())
            ))

            ->addEventListener(FormEvents::POST_SUBMIT, array($this,'onPostSubmit'))

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionPrerequis::class,
        ]);
    }

    public function onPostSubmit(FormEvent $formEvent){
        /** @var QuestionPrerequis $data */
        $data = $formEvent->getData();

        $data->getQuestion()->setThematique($data->getReponse()->getQuestion()->getThematique());
        $data->setOptionnel(false);
    }
}
