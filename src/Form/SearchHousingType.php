<?php

namespace App\Form;

use App\Data\SearchHousingData;
use App\Form\Type\CommodityType;
use App\Form\Type\HousingTypeType;
use App\Form\Type\NumberOfRoomsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHousingType extends AbstractType
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
            ->add('house_type', HousingTypeType::class, [
                'required' => false,
                'label' => 'Type de logement',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('disponibility', ChoiceType::class, [
                'choices' => [
                    'Immédiat' => true,
                ],
                'multiple' => true,
                'required' => false,
                'expanded' => true,
                'label' => 'Disponibilité',
            ])
            ->add('commodity', CommodityType::class, [
                'required' => false,
                'label' => 'Commodité',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('numberOfRooms', NumberOfRoomsType::class, [
                'required' => false,
                'label' => 'Nombre de chambres',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchHousingData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
