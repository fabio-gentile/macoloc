<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NewsletterEmailSameAsUser extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $messageEmail = 'L\'adresse e-mail spécifiée ne correspond pas à votre compte utilisateur.';
    public string $messageSignIn = 'Vous devez être connecté pour vous inscrire à la newsletter.';
}
