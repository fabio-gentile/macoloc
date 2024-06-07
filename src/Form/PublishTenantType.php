<?php

namespace App\Form;

use App\Entity\Tenant;
use App\Form\Type\AddressAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class PublishTenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', AddressAutocompleteType::class, [
                'label' => 'Adresse',
                'required' => true,
                'help' => 'La ville sera la seule information visible sur l’annonce.'
            ])
            ->add('about', AboutTenantType::class, [
                'label' => 'À propos',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 50,
                        'max' => 500,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Décrivez votre recherche en détail. Plus vous serez précis, plus vous aurez de chances de trouver un logement correspondant à vos attentes.',
                    'rows' => 10,
                ],
                'help' => 'Les annonces avec une description détaillée sont plus consultées.'
            ])
            ->add('image', FileType::class, [
                'label' => 'Images',
                'multiple' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2500K',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Seuls les fichiers jpeg, png, jpg et webp sont autorisés',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/jpg, image/webp',
                    'multiple' => false,
                ],
                'help' => 'Optionnel. Dimensions recommandées: 800x600 pixels. Taille maximale: 2 mégabytes.',
            ])
        ;
    }
}
