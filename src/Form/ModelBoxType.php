<?php

namespace App\Form;

use App\Entity\Calendar\ModelBox;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path')
            ->add('type')
            ->add('coordX')
            ->add('coordY')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelBox::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'modelBoxType';
    }
}
