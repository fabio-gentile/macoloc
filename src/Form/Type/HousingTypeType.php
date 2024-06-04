<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class HousingTypeType extends AbstractType
{
    public const HOUSING_TYPE_CHOICES = [
        'Maison' => 'Maison',
        'Appartement' => 'Appartement',
        'Studio' => 'Studio',
        'Duplex' => 'Duplex',
        'Villa' => 'Villa',
        'Résidence étudiante' => 'Residence étudiante',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::HOUSING_TYPE_CHOICES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
