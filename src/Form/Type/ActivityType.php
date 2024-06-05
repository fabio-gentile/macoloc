<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class ActivityType extends AbstractType
{
    public const ACTIVITY_CHOICES = [
        'Salarié' => 'Salarié',
        'Indépendant' => 'Indépendant',
        'Étudiant' => 'Étudiant',
        'Sans activité' => 'Sans activité',
        'Autre' => 'Autre'
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => self::ACTIVITY_CHOICES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
