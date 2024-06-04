<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class CommodityType extends AbstractType
{
    public const COMMODITY_CHOICES = [
        'Wi-Fi' => 'Wi-Fi',
        'Meublé' => 'Meublé',
        'Parking' => 'Parking',
        'Ascenseur' => 'Ascenseur',
        'Machine à laver' => 'Machine à laver',
        'Lave-vaisselle' => 'Lave-vaisselle',
        'Climatisation' => 'Climatisation',
        'Garage' => 'Garage',
        'Jardin' => 'Jardin',
        'Piscine' => 'Piscine'
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::COMMODITY_CHOICES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
