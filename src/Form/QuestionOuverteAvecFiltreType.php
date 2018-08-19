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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class QuestionOuverteAvecFiltreType extends QuestionAbstractType
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

            ->add('reponses',HiddenType::class, array(
                'data' => null,

            ))

            ->add('reponsePreRequise',CollectionType::class,array(
                'label' => 'Filtres',
                'attr' => array('data-collection'=>true),
                'entry_type' => FiltreType::class,
                'entry_options' => array('label'=>false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => array(new NotBlank(), new NotNull())
            ))

            ->addEventListener(FormEvents::PRE_SUBMIT,array($this,'onPreSubmit'))
            ->addEventListener(FormEvents::POST_SET_DATA, array($this,'onPostSetData'))
        ;
    }

    public function onPreSubmit(FormEvent $formEvent){
        $data = $formEvent->getData();
        $reponse = null;

        $reponse = new ReponsesOuverte();
        $reponse->setType($this->manager->getRepository(Type::class)->find($data["reponses_type"]));

        $collection = new ArrayCollection();
        $collection->add($reponse);
        $data['reponses'] = $collection;

        $formEvent->setData($data);

    }

    public function onPostSetData(FormEvent $formEvent){
        /** @var Question|null $data */
        $data = $formEvent->getData();

        if($data !== null){
            /** @var ReponsesOuverte $reponse */
            $reponse = $data->getReponses()->first();
            $tab = $formEvent->getData();

            $this->setDataAt($formEvent->getForm(), 'reponses_type', 'data', $reponse->getType());


        }
    }

}
