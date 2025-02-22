<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('pseudo', TextType::class, [
            'label' => 'Pseudo',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le pseudo est obligatoire']),
                new Assert\Length([
                    'min' => 3,
                    'max' => 30,
                    'minMessage' => 'Le pseudo doit contenir au moins 3 caractères',
                    'maxMessage' => 'Le pseudo ne peut pas dépasser 30 caractères'
                ])
            ]
        ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide'])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle utilisateur',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true, // Affiche des boutons radio
                'multiple' => true, // Permet de sélectionner plusieurs rôles
                'attr' => ['class' => 'form-check']
            ])
            ->add('password',RepeatedType::class,['type'=> PasswordType::class,
                'label' => 'Mot de passe',
                'mapped' => false, // Évite d’écraser le mot de passe existant en édition
                'required'=>false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères',
                        
                    ])
                ]
            ])
          
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil',
                'required' => false,
                'mapped' => false, // Empêche la liaison directe avec l'entité
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Seuls les formats JPG et PNG sont autorisés'
                    ])
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Compte actif',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])

            ->add('sumbit',SubmitType::class,[
                'label'=>'Ajouter'
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
