<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NewsletterEmailSameAsUserValidator extends ConstraintValidator
{
    public function __construct(
        private Security $security
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NewsletterEmailSameAsUser $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user) {
            $this->context->buildViolation($constraint->messageSignIn)
                ->addViolation();
            return;
        }

        $userEmail = $user->getEmail();

        if ($value !== $userEmail) {
            $this->context->buildViolation($constraint->messageEmail)
                ->addViolation();
        }
    }
}
