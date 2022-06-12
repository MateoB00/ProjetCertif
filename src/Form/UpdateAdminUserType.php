<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UpdateAdminUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Modérateur' => 'ROLE_MODO',
                    'Utilisateur' => null,
                    'Bannis' => 'ROLE_BANNED',
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('nom')
            ->add('prenom')
            ->add(
                'isVerified',
                CheckboxType::class,
                [
                    'label' => 'Email vérifier ? ',
                    'required' => false
                ]
            )
            ->add('adresse')
            ->add('ville')
            ->add('numtel')
            // ->add('genre')
            // ->add('anniversaire')
            ->add(
                'estcoach',
                CheckboxType::class,
                [
                    'label' => 'Deviens coach ?',
                    'required' => false
                ]
            )
            ->add('toncoach', EntityType::class, [
                'class' => User::class,
                "choice_label" => "findcoach",
                'label' => 'Son coach',
                'required' => false
            ])

            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
