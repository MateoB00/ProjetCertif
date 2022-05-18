<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('oldPassword', PasswordType::class, array(
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank,
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Ancien Mot de passe *'
                ],
                'required' => true,

            ))

            ->add('plainPassword', RepeatedType::class, array(

                'mapped' => false,
                'label' => false,
                'type' => PasswordType::class,
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
                        'message' => 'Votre mot de passe doit contenir au moins un caractère spécial.',
                    ]),
                ],
                'first_options'  => ['attr' => [
                    'placeholder' => 'Nouveau Mot de passe *',
                    'class' => 'password-field',
                    'class' => 'input',
                ]],
                'second_options' => ['attr' => [
                    'placeholder' => 'Confirmation du nouveau Mot de passe *',
                    'class' => 'password-field',
                    'class' => 'input',
                ]],
                'invalid_message' => 'Les deux mots de passe doivent être identiques',
                'options' => array(
                    'label' => false,
                ),
                'required' => true,
            ))

            ->add('submit', SubmitType::class, array(
                'attr' => array(
                    'class' => 'inputBtn'
                )

            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
