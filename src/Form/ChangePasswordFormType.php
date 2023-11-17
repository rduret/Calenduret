<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'label' => 'Nouveau mot de passe', 
                    'attr' => [
                        'placeholder' => "Nouveau mot de passe", 
                        'pattern' => '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[\\#\\?\\!\\@\\$\\%\\^\\&\\*\\-]).{8,}$', 
                        'minLength' => 8]
                    ],
                'second_options' => [
                    'label' => 'Vérification du mot de passe *', 
                    'attr' => [
                        'placeholder' => "Verification du nouveau mot de passe", 
                    ]],
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                        'message' => 'Le mot de passe doit contenir obligatoirement un caractère en minuscule, un en majuscule, un chiffre et un caractère spécial (!@#$%^&*-)'
                    ])
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
