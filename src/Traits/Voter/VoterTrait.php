<?php

namespace App\Traits\Voter;

use App\Entity\Settings\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait VoterTrait
{
    private ?array $allRoles = [];

    /**
     * @param Role $role
     *
     * @return void
     */
    private function getAllAuthorizations(Role $role): void
    {
        if (empty($role->getAuthorizations())) {
            return;
        }

        foreach ($role->getAuthorizations() as $authorization) {

            if (!in_array($authorization, $this->allRoles, true)) {
                $this->allRoles[] = $authorization;
            }
        }

        if (!$role->getChildRoles()->isEmpty()) {

            foreach ($role->getChildRoles() as $childRole) {
                $this->getAllAuthorizations($childRole);
            }
        }
    }

    /**
     * @param string $authorizationLevel
     * @param $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    private function checkLevelAuthorization(string $authorizationLevel, $subject, UserInterface $user): bool
    {
        if ($authorizationLevel === 'group'
            || !method_exists($subject, 'getCreatedBy')
            || null === $subject->getCreatedBy()
        ) {
            return true;
        }

        if (($authorizationLevel === 'society' || !method_exists($subject->getCreatedBy(), 'getClinic'))
            && $subject->getCreatedBy()->getClinic() === $user->getClinic())  {
            return true;
        }

        if ($authorizationLevel === 'user' && $subject->getCreatedBy() === $user)  {
            return true;
        }

        return false;
    }
}