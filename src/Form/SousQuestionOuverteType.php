<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuestionPrerequis;
use App\Entity\ReponsesFerme;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class SousQuestionOuverteType extends AbstractType
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
                'class' => ReponsesFerme::class,
                'choice_label' => 'texte'
            ))


            //->add('reponse',HiddenType::class)

            ->add('question',QuestionOuverteType::class, array(
                'label' => false,
                'constraints' => array(new Valid())
            ))

            ->addEventListener(FormEvents::PRE_SUBMIT,array($this,'onPreSubmit'))

        ;

    }

    public function onPreSubmit(FormEvent $formEvent)
    {
        $data = $formEvent->getData();

        if(array_key_exists('reponse_help', $data)){
            /** @var ReponsesFerme|null $reponse */
            $reponse = $this->manager->getRepository(ReponsesFerme::class)->find($data['reponse_help']);
            if($reponse!==null){
                dump($reponse);
                $data['reponse'] = $reponse;
            }
        }

        $formEvent->setData($data);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionPrerequis::class,
        ]);
    }
}
