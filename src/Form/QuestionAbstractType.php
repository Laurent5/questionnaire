<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Thematique;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class QuestionAbstractType extends AbstractType
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
            ->add('thematique',EntityType::class,array(
                'class' => Thematique::class,
                'label' => "A quelle thématique appartient cette question ?",
                'choice_label' => 'nom'
            ))
            ->add('ordre',null,array(
                'label' => 'Numéro (unique) de la question'
            ))
            ->add('question', null, array(
                'label' => 'Intitulée de la question'
            ))
            ->add("aide",null,array(
                'label' => 'Eventuelle(s) précision(s) à afficher',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
