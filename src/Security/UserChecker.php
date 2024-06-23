<?php

namespace App\Security;

use App\Entity\UserAccount;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof UserAccount) {
            return;
        }

        if (in_array('ROLE_REGISTRATION_EMAIL_WAITING', $user->getRoles())) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas encore vérifié. Veuillez vérifier votre email.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof UserAccount) {
            return;
        }
    }
}
