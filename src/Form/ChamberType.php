<?php

namespace App\Form;

use App\Entity\Chamber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChamberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avaible_at', DateType::class, [
//                TODO: Ajouter un date picker
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de disponibilité',
                'empty_data' => new \DateTime(),
            ])
            ->add('price', MoneyType::class, [
                'required' => true,
                'label' => 'Loyer',
                'attr' => [
                    'help' => 'Charges comprises.',
                ],
            ])
            ->add('caution', MoneyType::class, [
                'required' => true,
                'label' => 'Caution',
            ])
            ->add('surface_area', NumberType::class, [
                'required' => true,
                'label' => 'Surface',
                'help' => 'En m²',
            ])
            ->add('furnished', ChoiceType::class, [
                'required' => true,
                'label' => 'Meublé',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'placeholder' => 'Sélectionner',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chamber::class,
        ]);
    }
}
