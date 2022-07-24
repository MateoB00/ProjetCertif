<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'input',
                        'placeholder' => 'Nouveau mot de passe *'
                    ],
                    'label' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'S\'il vous plait entrez un mot de passe.',
                        ]),
                        new Length([
                            'min' => 6,
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/\d/',
                            'message' => 'Votre mot de passe doit contenir au moins un chiffre.'
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule.'
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule.'
                        ]),
                        new Regex([
                            'pattern' => '/[!@#$%^&*]/',
                            'message' => 'Votre mot de passe doit contenir au moins un caractère spécial.',
                        ]),
                    ],

                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'input',
                        'placeholder' => 'Confirmez votre mot de passe *'
                    ],
                    'label' => false,
                ],
                'invalid_message' => 'Le mot de passe est invalide.',
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Reinitialisez !',
                'attr' => [
                    'class' => 'inputBtn',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([]);
    }
}
