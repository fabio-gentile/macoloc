<?php

namespace App\Form;

use App\Form\Type\HousingTypeType;
use App\Form\Type\NumberOfRoomsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class HousingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_housing', HousingTypeType::class, [
                'required' => true,
                'label' => 'Type de logement',
                'placeholder' => 'Sélectionner',
            ])
            ->add('number_of_rooms', NumberOfRoomsType::class, [
                'required' => true,
                'label' => 'Nombre de pièces',
                'placeholder' => 'Sélectionner',
                'total' => true,
            ])
            ->add('surface_area', NumberType::class, [
                'required' => true,
                'label' => 'Surface',
                'help' => 'En m²',
            ])
        ;
    }
}
