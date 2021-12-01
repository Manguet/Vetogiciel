<?php

namespace App\Interfaces\Settings;

use App\Entity\Settings\Role;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface RoleTableInterface
{
    /**
     * @param Request $request
     * @param Role $role
     *
     * @return void
     */
    public function updateAuthorizationsInRole(Request $request, Role $role): void;
}