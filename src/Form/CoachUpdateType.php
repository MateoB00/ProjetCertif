<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CoachUpdateType extends AbstractType {
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
            ->add('adresse')
            ->add('ville')
            ->add('numtel')
            ->add('coachpdp', FileType::class, [
                'required' => false,
                'data_class' => null,
                'label' => 'photo de profil',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                    ]),
                    new Image
                ]
            ])
            // ->add('genre')
            // ->add('anniversaire')
            ->add(
                'estcoach',
                CheckboxType::class,
                [
                    'label' => 'Est coach ? ',
                    'required' => false
                ]
            )
            ->add('diplome')

            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
