<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class GenderType extends AbstractType
{
    public const GENDER_CHOICES = [
        'Homme' => 'Homme',
        'Femme' => 'Femme',
        'Autre' => 'Autre',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::GENDER_CHOICES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
