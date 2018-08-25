<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Nom de la figure'))
            ->add('description', TextareaType::class, array('label' => 'Description de la figure'))
            ->add('medias', CollectionType::class, array(
                'by_reference' => false,
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('videos', CollectionType::class, array(
                'by_reference' => false,
                'entry_type' => MediaVideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('categories' , EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
                $trick = $event->getData();
                $form = $event->getForm();

                if($trick || null != $trick->getId()) {
                    $field = $form->get('medias');
                    $options = $field->getConfig()->getOptions();            // get the options
                    $options['required'] = false ;           // change the label
                    $form->add('medias', CollectionType::class, $options);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'label' => false,
        ]);
    }
}
