<?php

namespace App\Form;

use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountType extends AbstractType
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
                'required' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom',
                    ]),
                ],
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                ],
                'placeholder' => false,
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
                'required' => false
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'choice',
                'label' => 'Date de naissance',
                'years' => range(2024, 1900),
                'format' => 'dd MMMM yyyy',
                'placeholder' => false,
                'choice_translation_domain' => true,
                'invalid_message' => 'Veuillez renseigner une date de naissance valide',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre date de naissance',
                    ]),
                ],
                'required' => false
            ])
            ->add('currentPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Mot de passe actuel',
                'toggle' => true,
                'hidden_label' => null,
                'visible_label' => null,
                'visible_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="custom-toggle-password-icon" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2 12s3-7 10-7s10 7 10 7s-3 7-10 7s-10-7-10-7"/><circle cx="12" cy="12" r="3"/></g></svg>',
                'hidden_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="custom-toggle-password-icon" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24m-3.39-9.04A10 10 0 0 1 12 5c7 0 10 7 10 7a13.2 13.2 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.5 13.5 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61M2 2l20 20"/></g></svg>',
                'button_classes' => ['custom-toggle-password-button'],
                'mapped' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Nouveau mot de passe',
                'toggle' => true,
                'hidden_label' => null,
                'visible_label' => null,
                'visible_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="custom-toggle-password-icon" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2 12s3-7 10-7s10 7 10 7s-3 7-10 7s-10-7-10-7"/><circle cx="12" cy="12" r="3"/></g></svg>',
                'hidden_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="custom-toggle-password-icon" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24m-3.39-9.04A10 10 0 0 1 12 5c7 0 10 7 10 7a13.2 13.2 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.5 13.5 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61M2 2l20 20"/></g></svg>',
                'button_classes' => ['custom-toggle-password-button'],
                'mapped' => false,
                'help' => 'Votre mot de passe contenir au moins 6 caractères.'
            ])
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
