<?php

namespace App\Service\Authorizations;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminAuthorizationServices
{
    /**
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function testBasicAuthorizationByUser(?UserInterface $user): bool
    {
        if (!$user) {

            return false;
        }

        if ($user instanceof Veterinary) {

            return true;
        }

        if ($user instanceof Employee) {

            return true;
        }

        return false;
    }
}