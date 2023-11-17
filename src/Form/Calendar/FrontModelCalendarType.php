<?php

namespace App\Form\Calendar;

use App\Form\ModelBoxType;
use App\Entity\Calendar\ModelCalendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FrontModelCalendarType extends AbstractType
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
                'empty_data' => '#000'
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
            ->add('modelBoxes', CollectionType::class, [
                'entry_type' => ModelBoxType::class,
                'label' => 'Liste des cases',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'collection-boxes',
                ),
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
