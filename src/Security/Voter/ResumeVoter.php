<?php

namespace App\Security\Voter;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\Resume;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResumeVoter extends Voter
{
    public const CREATE = "create";
    public const EDIT = "edit";

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT])
            && $subject instanceof Application;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|Particular|Company|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Resume $resume */
        $resume = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($user);
            case self::EDIT:
                return $this->canEdit($resume, $user);
        }

        return false;
    }

    private function canEdit(Resume $resume, User $user): bool
    {
        return $this->canCreate($user) && $resume->isOwner($user);
    }

    private function canCreate(User $user): bool
    {
        return $user->isParticular();
    }
}
