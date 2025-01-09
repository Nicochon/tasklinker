<?php
namespace App\Security;

use App\Entity\Project;
use App\Entity\Users;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjectVoter extends Voter
{
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Users) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return $subject->getUsers()->contains($user);
    }
}