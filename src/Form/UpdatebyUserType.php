<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdatebyUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            // ->add('email', TextType::class, [
            //     'label' => false,
            //     'attr' => [
            //         'class' => 'input',
            //         'placeholder' => 'Email'
            //     ],
            // ])
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nom'
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Prénom'
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Adresse'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Ville'
                ]
            ])
            ->add('numtel', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Numéro de téléphone'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Entrez un numéro de téléphone valide'
                    ]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => array(
                    'class' => 'inputBtn'
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
