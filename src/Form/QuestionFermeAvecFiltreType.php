<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\Entity\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class QuestionFermeAvecFiltreType extends QuestionAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('reponsePreRequise',CollectionType::class,array(
                'label' => 'Filtres',
                'attr' => array('data-collection'=>true),
                'entry_type' => FiltreType::class,
                'entry_options' => array('label'=>false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => array(new NotBlank(), new NotNull(), new Count(array(
                    'min' => 1,
                    'minMessage' => "Une réponse avec filtre, doit contenir au moins 1 filtre"
                )))
            ))
        ;
        parent::buildForm($builder,$options);

        $builder
            ->add('multiple',CheckboxType::class,array(
                'label' => 'Est-ce que les répondants peuvent choisirs plusieurs réponses ?',
                'mapped' => false,
                'required' => false
            ))

            ->add('reponses',CollectionType::class,array(
                'label' => 'Réponses possibles',
                'attr' => array('data-collection'=>true),
                'entry_type' => ReponseFermeeType::class,
                'entry_options' => array('label'=>false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => array(new NotBlank(), new NotNull())
            ))


            ->addEventListener(FormEvents::PRE_SUBMIT,array($this,'onPreSubmit'))
            ->addEventListener(FormEvents::POST_SUBMIT,array($this,'onPostSubmit'))
        ;
    }

    public function onPreSubmit(FormEvent $formEvent){
        /** @var Question $data */
        $data = $formEvent->getData();
        $this->multiple = array_key_exists('multiple', $data);

    }

    public function onPostSubmit(FormEvent $formEvent){

        /** @var Question $data */
        $data = $formEvent->getData();

        /** @var ReponsesFerme $reponses */
        foreach ($data->getReponses() as $reponses){
            $reponses->setMultiple($this->multiple);
        }

    }

}
