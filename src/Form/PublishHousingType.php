<?php

namespace App\Form;

use App\Form\Type\AddressAutocompleteType;
use App\Form\Type\CommodityType;
use App\Form\Type\DescriptionType;
use App\Validator\NotEmptyCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;

class PublishHousingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', AddressAutocompleteType::class, [
                'label' => 'Adresse',
                'required' => true,
                'help' => 'La ville sera la seule information visible sur l’annonce.'
            ])
            ->add('housing', HousingType::class, [
                'label' => false,
            ])
            ->add('chambers', CollectionType::class, [
                'entry_type' => ChamberType::class,
                'by_reference' => false,
                'label' => 'Chambre',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
                'attr' => [
                    'data-controller' => 'form-collection',
                    'data-form-collection-add-label-value' => 'Ajouter une chambre',
                    'data-form-collection-delete-label-value' => 'Supprimer la chambre',
                ],
                'constraints' => [
                    new NotEmptyCollection([
                        'message' => 'Au moins une chambre est requise.',
                    ]),
                ],
            ])
            ->add('commodity', CommodityType::class, [
                'label' => 'Commodités',
                'expanded' => true,
                'multiple' => true,
                'constraints' => [
                    new Choice([
                        'choices' => CommodityType::COMMODITY_CHOICES,
                        'multiple' => true,
                        'message' => 'Veuillez choisir une ou plusieurs commodités.'
                    ]),
                ],
            ])
            ->add('other', ChoiceType::class, [
                'label' => 'Autre',
                'choices' => [
                    'Animaux acceptés' => 'Animaux',
                    'Fumeurs acceptés' => 'Fumeurs',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('description', DescriptionType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('images', FileType::class, [
                'label' => 'Images',
                'multiple' => true,
                'mapped' => false,
                // TODO: rendre optionnel pour l'édition
//                'required' => true,
                'constraints' => [
                    new All([
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
                            ])
                        ]
                    ]),
                    new NotEmptyCollection([
                        'message' => 'Au moins une image est requise.',
                    ])
                ],
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/jpg, image/webp',
                    'multiple' => 'multiple',
                ],
                'help' => 'Dimensions recommandées: 800x600 pixels. Taille maximale: 2,5 mégabytes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
