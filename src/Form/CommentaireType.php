<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', TextareaType::class, [
                'required' => true,
                'label' => 'Votre commentaire', // Ajout du label ici
                'attr' => [
                    'class' => 'form-control',  // Classe Bootstrap
                    'rows' => 3,               // Nombre de lignes du textarea
                    'placeholder' => 'Écrivez votre commentaire ici...', // Texte d'indication
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier',  
                'attr' => [
                    'class' => 'btn btn-primary', // Style du bouton
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
