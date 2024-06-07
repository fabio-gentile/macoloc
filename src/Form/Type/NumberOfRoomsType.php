<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
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

    public const TOTAL_NUMBER_OF_ROOMS_CHOICES = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::NUMBER_OF_ROOMS_CHOICES,
            'total' => false,
        ]);

        $resolver->setAllowedTypes('total', 'bool');
        $resolver->setNormalizer('choices', function (Options $options, $value) {
            return $options['total'] ? self::TOTAL_NUMBER_OF_ROOMS_CHOICES : self::NUMBER_OF_ROOMS_CHOICES;
        });
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
