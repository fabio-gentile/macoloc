<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Entity\FrenchCity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class AddressAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => FrenchCity::class,
            'searchable_fields' => ['city'],
            'label' => 'Ville',
            'choice_label' => 'city',
            'multiple' => false,
            'attr' => [
                'placeholder' => 'Ville',
            ],
            'constraints' => [
                new NotBlank((['message' => 'Veuillez sélectionner une ville.'])),
            ],
            'security' => 'ROLE_USER',
            'max_results' => 10,
            'min_characters' => 2,
            'preload' => true,
            'required' => true,
            'help' => 'La ville sera la seule information visible sur l’annonce.'
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
