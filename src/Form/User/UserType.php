<?php

namespace App\Form\User;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom *',
                'required' => true
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            if (!$user || null === $user->getId()) {
                $form->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne correspondent pas.',
                    'first_options' => ['label' => 'Mot de passe *', 'attr' => ['placeholder' => "Mot de passe", 'pattern' => '(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$', 'minLength' => 8]],
                    'second_options' => ['label' => 'Vérification du mot de passe *', 'attr' => ['placeholder' => "Verification du mot de passe", 'pattern' => '(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$', 'minLength' => 8]],
                    'options' => ['attr' => ['autocomplete' => 'new-password']],
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$/',
                            'message' => 'Le mot de passe doit contenir obligatoirement un caractère en minuscule, un en majuscule, un chiffre et un caractère spécial (@$!%*?&)'
                        ])
                    ],
                ]);
            } else {
                $form->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne correspondent pas.',
                    'first_options' => ['label' => 'Mot de passe *', 'attr' => ['placeholder' => "Mot de passe", 'pattern' => '(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$', 'minLength' => 8]],
                    'second_options' => ['label' => 'Vérification du mot de passe *', 'attr' => ['placeholder' => "Verification du mot de passe", 'pattern' => '(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$', 'minLength' => 8]],
                    'options' => ['attr' => ['autocomplete' => 'new-password']],
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d!?;$@&%#_-]{8,}$/',
                            'message' => 'Le mot de passe doit contenir obligatoirement un caractère en minuscule, un en majuscule, un chiffre et un caractère spécial (!@#$%^&*-)'
                        ])
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
