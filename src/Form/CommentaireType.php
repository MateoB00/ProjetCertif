<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank,
                ],
                "attr" => [
                    "class" => "input",
                    'placeholder' => 'Commentaire *'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Poste un commentaire !',
                "attr" => [
                    "class" => "inputBtn"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
