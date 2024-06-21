<?php

namespace App\Security\Voter;

use App\Entity\Housing;
use App\Entity\HousingImage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class HousingVoter extends Voter
{
    public const EDIT = 'HOUSING_EDIT';
    public const VIEW = 'HOUSING_VIEW';
    public const DELETE = 'HOUSING_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::VIEW, self::EDIT])) {
            return true;
        }

        if ($subject instanceof Housing || $subject instanceof HousingImage) {
            return true;
        }

        return false;
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
                if ($subject instanceof Housing) {
                    return $subject->getUser() === $user;
                };

                if ($subject instanceof HousingImage) {
                    return $subject->getHousing()->getUser() === $user;
                };
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
