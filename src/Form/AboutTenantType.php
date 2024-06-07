<?php

namespace App\Form;

use App\Entity\Tenant;
use App\Form\Type\ActivityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;

class AboutTenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('activity', ActivityType::class, [
                'required' => true,
                'label' => 'Activité',
                'placeholder' => 'Sélectionner',
                'constraints' => [
                    new Choice([
                        'choices' => ActivityType::ACTIVITY_CHOICES,
                        'message' => 'Veuillez choisir une activité.'
                    ]),
                ],
            ])
            ->add('budget', MoneyType::class, [
                'required' => true,
                'label' => 'Budget',
                'help' => 'Charges comprises',
                'constraints' => [
                    new GreaterThan([
                        'value' => 50,
                        'message' => 'Le budget ne peut pas être inférieur à 50€.'
                    ]),
                ],
            ])
        ;
    }
}
