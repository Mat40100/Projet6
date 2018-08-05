<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
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
