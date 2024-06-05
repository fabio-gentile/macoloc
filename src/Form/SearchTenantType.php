<?php

namespace App\Form;

use App\Data\SearchTenantData;
use App\Form\Type\ActivityType;
use App\Form\Type\GenderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchTenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('min_price', MoneyType::class, [
                'required' => false,
                'label' => 'Prix minimum',
            ])
            ->add('max_price', MoneyType::class, [
                'required' => false,
                'label' => 'Prix maximum',
            ])
            ->add('gender', GenderType::class, [
                'required' => false,
                'label' => 'Genre',
                'expanded' => true,
                'placeholder' => 'Tous'
            ])
            ->add('min_age', IntegerType::class, [
                'required' => false,
                'label' => 'Âge minimum',
            ])
            ->add('max_age', IntegerType::class, [
                'required' => false,
                'label' => 'Âge maximum',
            ])
            ->add('activity', ActivityType::class, [
                'required' => false,
                'label' => 'Activité',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchTenantData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
