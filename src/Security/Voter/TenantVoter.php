<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TenantVoter extends Voter
{
    public const EDIT = 'TENANT_EDIT';
    public const VIEW = 'TENANT_VIEW';
    public const DELETE = 'TENANT_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Tenant;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DELETE:
            case self::EDIT:
                return $subject->getUser() === $user;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
