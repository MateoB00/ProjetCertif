<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Partieblog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PartieblogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('titrepartie', TextType::class, [
                'attr' => [
                    'placeholder' => '1. Premier titre du blog'
                ]
            ])
            ->add('contenupartie', TextareaType::class);
        // ->add('blog', EntityType::class, [
        //     'class' => Blog::class,
        //     'choice_label' => 'titre'
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partieblog::class,
        ]);
    }
}
