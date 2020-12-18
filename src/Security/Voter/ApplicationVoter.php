<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\Application;
use App\Service\AuthService;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApplicationVoter extends Voter
{
    public const CREATE = "create";
    public const VIEW = "view";
    public const EDIT = "edit";

    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::VIEW, self::EDIT]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|Particular|Company|null $user */
        $user = $this->auth->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Application $application */
        $application = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($user);
            case self::VIEW:
                return $this->canView($application, $user);
            case self::EDIT:
                return $this->canEdit($application, $user);
        }

        return false;
    }

    private function canCreate(User $user): bool
    {
        return $user->isParticular();
    }

    private function canView(Application $application, User $user): bool
    {
        return ($user->isCompany() || $application->isOwner($user->getId()));
    }

    private function canEdit(Application $application, User $user): bool
    {
        return $user->isCompany() && $application->isOwner($user->getId());
    }
}
