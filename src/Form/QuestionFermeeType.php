<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\ReponsesFerme;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class QuestionFermeeType extends QuestionAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);

        $builder
            ->add('multiple',CheckboxType::class,array(
                'label' => 'Est-ce que les répondants peuvent choisirs plusieurs réponses ?',
                'mapped' => false,
                'required' => false
            ))
            ->add('choix_possibles',CollectionType::class,array(
                'label' => 'Réponses possibles',
                'attr' => array('data-collection'=>true),
                'mapped' => false,
                'entry_type' => ReponseFermeeType::class,
                'entry_options' => array('label'=>false),
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => array(new NotBlank(), new NotNull())
            ))
            ->add('reponses',HiddenType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT,array($this,'onPreSubmit'))
        ;
    }

    public function onPreSubmit(FormEvent $formEvent){
        $data = $formEvent->getData();

        $multiple = array_key_exists('multiple',$data);
        $reponses = new ArrayCollection();

        if(array_key_exists('choix_possibles',$data)) {
            foreach ($data['choix_possibles'] as $choix) {
                $reponse = new ReponsesFerme();
                $reponse->setMultiple($multiple);
                $reponse->setTexte($choix['texte']);
                $reponses->add($reponse);
            }
        }

        $data['reponses'] = $reponses;
        $formEvent->setData($data);
    }

}
