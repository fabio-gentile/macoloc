<?php

namespace App\Form;

use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre prénom',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom',
                    ]),
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                ],
                'expanded' => true,
                'invalid_message' => 'Veuillez renseigner une langue valide.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre genre',
                    ]),
                ],
                'attr' => [
                    'class' => 'flex flex-wrap gap-4'
                ],
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'choice',
                'label' => 'Date de naissance',
                'years' => range(2024, 1900),
                'format' => 'dd MMMM yyyy',
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'choice_translation_domain' => true,
                'invalid_message' => 'Veuillez renseigner une date de naissance valide',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre date de naissance',
                    ]),
                    new LessThan([
                        'value' => -18 . ' years',
                        'message' => 'Vous devez avoir au moins 18 ans pour vous inscrire.'
                    ])
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAccount::class,
            'translation_domain' => 'forms'
        ]);
    }
}
