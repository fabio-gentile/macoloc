<?php

namespace App\Form;

use App\Form\Type\HousingTypeType;
use App\Form\Type\NumberOfRoomsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;

class HousingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_housing', HousingTypeType::class, [
                'required' => true,
                'label' => 'Type de logement',
                'placeholder' => 'Sélectionner',
                'constraints' =>
                    new Choice([
                            'choices' => HousingTypeType::HOUSING_TYPE_CHOICES,
                            'message' => 'Veuillez choisir un type de logement.',
                    ]),
            ])
            ->add('number_of_rooms', NumberOfRoomsType::class, [
                'required' => true,
                'label' => 'Nombre de pièces',
                'placeholder' => 'Sélectionner',
                'total' => true,
                'constraints' =>
                    new Choice([
                            'choices' => NumberOfRoomsType::NUMBER_OF_ROOMS_CHOICES,
                            'message' => 'Veuillez choisir un nombre de pièces.',
                    ]),
            ])
            ->add('surface_area', NumberType::class, [
                'required' => true,
                'label' => 'Surface',
                'help' => 'En m²',
                'constraints' => [
                    new GreaterThan([
                        'value' => 5,
                        'message' => 'La surface doit être supérieure à 5m².',
                    ])
                ]
            ])
        ;
    }
}
