<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessagescoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank
                ],
                "attr" => [
                    "class" => "input",
                    'placeholder' => 'Sujet - Titre *'
                ],
                'required' => true,

            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank
                ],
                "attr" => [
                    "class" => "input",
                    'placeholder' => 'Message *'
                ]
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer !',
                "attr" => [
                    "class" => "inputBtn"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
