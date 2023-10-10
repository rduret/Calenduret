<?php

namespace App\Form;

use App\Entity\Calendar\ModelBox;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ModelBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Nouveau fichier (20Mo max)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/*',
                            'video/*',
                            'audio/*',
                        ],
                        'mimeTypesMessage' => 'Fichier non valide (vérifiez l\'extension)',
                    ])
                ],
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'position',
                ],
            ])
            ->add('type')
            ->add('coordX', TextType::class, [
                'attr' => [
                    'class' => 'coordX',
                ]
            ])
            ->add('coordY', TextType::class, [
                'attr' => [
                    'class' => 'coordY',
                ]
            ])
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
