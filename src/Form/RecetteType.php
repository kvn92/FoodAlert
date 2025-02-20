<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de la recette',
                'required' => false, // Facultatif
                'mapped' => false, // Pour éviter les erreurs sur l'entité
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux (10 Mo max)',
                        'mimeTypes' => ['image/jpeg', 'image/png','image/jpg'],
                        'mimeTypesMessage' => 'Seuls les fichiers JPEG , PNG  et JPG sont autorisés',
                    ])
                ]
            ])
            ->add('preparation', TextareaType::class, [
                'label' => 'Préparation',
                'required' => true
            ]);

        // Afficher "isActive" seulement en mode édition
        if ($options['is_edit']) {
            $builder->add('isActive', ChoiceType::class, [
                'label' => 'Recette active ?',
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false
                ],
                'expanded' => true, // Affiche des boutons radio
                'multiple' => false
            ])
            ;
        }

    }
   
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'is_edit' => false, // Ajout de cette option avec une valeur par défaut
        ]);
    }
}

