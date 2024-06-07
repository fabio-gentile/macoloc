<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotEmptyCollectionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (empty($value) || count($value) === 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
