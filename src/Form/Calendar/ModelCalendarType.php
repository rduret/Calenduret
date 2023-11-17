<?php

namespace App\Form\Calendar;

use App\Entity\Calendar\ModelCalendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ModelCalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *',
                'required' => true
            ])
            ->add('color', HiddenType::class, [
                'label' => 'Couleur * (bordure et interieur)',
                'required' => true,
            ])     
            ->add('file', FileType::class, [
                'label' => 'Image de fond * (jpg, jpeg, png)',
                'label_attr' => ['class' => 'form-label'],
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un document valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelCalendar::class,
        ]);
    }
}
