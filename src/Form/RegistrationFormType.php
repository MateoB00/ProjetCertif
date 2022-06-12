<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder

            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nom *'
                ],

            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Prénom *'
                ],

            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank,
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
                        'message' => 'Votre mot de passe doit contenir au moins un caractère spécial.'
                    ]),
                ],
                'attr' => ['autocomplete' => 'new-password'],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Mot de passe *'
                ],
                'constraints' => [
                    new NotBlank,
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'required' => true,
                'label' => false,
                'constraints' => [
                    new NotBlank
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => '2 Avenue des Champs Elysée'
                ]
            ])
            ->add('ville', TextType::class, [
                'required' => true,
                'label' => false,
                'constraints' => [
                    new NotBlank
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Paris 75008'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Email *'
                ],
            ])
            ->add('numtel', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => '06.18.27.81.99'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Entrez un numéro de téléphone valide'
                    ]),
                    new NotBlank
                ]
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Femme' => '1',
                    'Homme' => '0'
                ],
                'attr' => [
                    'class' => 'input2',
                ],
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank
                ],
            ])
            ->add('anniversaire', BirthdayType::class, [
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour'
                ],
                'label' => false,
                'attr' => [
                    'class' => 'input1 my-3',
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos mentions légales',
                    ]),
                    new NotBlank
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer !',
                'attr' => [
                    'class' => 'inputBtn',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
