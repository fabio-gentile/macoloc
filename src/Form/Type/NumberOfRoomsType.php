<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class NumberOfRoomsType extends AbstractType
{
    public const NUMBER_OF_ROOMS_CHOICES = [
        '1 chambre disponible' => '1',
        '2 chambres disponibles' => '2',
        '3 chambres disponibles' => '3',
        '4 chambres disponibles' => '4',
        '5+ chambres disponibles' => '5',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::NUMBER_OF_ROOMS_CHOICES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
