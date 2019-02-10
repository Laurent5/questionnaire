<?php

namespace App\Form;

use App\Entity\ReponsesFourniesIndividuellesOuverte;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $event->getForm()->add('categories',null,array(
            'label'=> 'categories',
            'choice_label' => 'categorie',
            'choices' => $event->getData()->getQuestions()->getCategories(),
            'expanded' => true
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReponsesFourniesIndividuellesOuverte::class,
        ]);
    }
}
