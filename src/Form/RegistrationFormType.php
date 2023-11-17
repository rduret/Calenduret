<?php

namespace App\Form;

use App\Entity\Auth\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Adresse e-mail *'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner une adresse mail',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom *'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un prénom',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom *'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un nom',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-check-input'],
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez valider les conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => ['label' => 'Mot de passe *', 'attr' => ['placeholder' => "Mot de passe", 'pattern' => '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[\\#\\?\\!\\@\\$\\%\\^\\&\\*\\-]).{8,}$', 'minLength' => 8]],
                'second_options' => ['label' => 'Vérification du mot de passe *', 'attr' => ['placeholder' => "Verification du mot de passe", 'pattern' => '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[\\#\\?\\!\\@\\$\\%\\^\\&\\*\\-]).{8,}$', 'minLength' => 8]],
                'options' => ['attr' => ['autocomplete' => 'new-password']],
                'required' => true,
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
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
