<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank,
                ],
                'attr' => [
                    'placeholder' => 'Nom *',

                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank,
                ],
                'attr' => [
                    'placeholder' => 'Prénom *',

                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'constraints' => [
                    new Email,
                    new NotBlank,
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email *',

                ]

            ])
            ->add('telephone', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Téléphone *'
                ],
                'constraints' => [
                    new NotBlank,
                    new Regex([
                        'pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Entrez un numéro de téléphone valide'

                    ])
                ]
            ])
            ->add('sujet', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 150
                    ]),
                    new NotBlank,
                ],
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Sujet *'
                ]
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new Length([
                        'max' => 1500
                    ]),
                    new NotBlank,
                ],
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Message *'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'inputBtn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
