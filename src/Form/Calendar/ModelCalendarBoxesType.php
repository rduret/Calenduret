<?php

namespace App\Form\Calendar;

use App\Entity\Calendar\ModelCalendar;
use App\Form\ModelBoxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelCalendarBoxesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modelBoxes', CollectionType::class, [
                'entry_type' => ModelBoxType::class,
                'label' => 'Liste des cases',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'my-selector',
                ),
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelCalendar::class,
        ]);
    }
}
