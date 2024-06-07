<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères.'
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Décrivez votre logement en détail. Plus vous serez précis, plus vous aurez de chances de trouver un locataire correspondant à vos attentes.',
                    'rows' => 10,
                ],
                'help' => 'Les annonces avec une description détaillée sont plus consultées.',
                'constraints' => [
                    new Length([
                        'min' => 50,
                        'max' => 1000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères.'
                    ])
                ],
            ])
        ;
    }
}
