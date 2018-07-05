<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\Entity\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionOuverteType extends QuestionAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);

        $builder
            ->add('reponses_type',EntityType::class,array(
                'class' => Type::class,
                'mapped' => false,
                'label' => 'De quelle type doit-être la réponse ?',
                'choice_label' => 'type'
            ))
            ->add('reponses',HiddenType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT,array($this,'onPreSubmit'))
        ;
    }

    public function onPreSubmit(FormEvent $formEvent){
        $data = $formEvent->getData();

        $reponse = new ReponsesOuverte();
        $reponse->setType($this->manager->getRepository(Type::class)->find($data["reponses_type"]));

        $collection = new ArrayCollection();
        $collection->add($reponse);
        $data['reponses'] = $collection;

        $formEvent->setData($data);

    }

}
