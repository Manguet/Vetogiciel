<?php

namespace App\DBAL\Types\Configuration;

use App\DBAL\EnumType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RoleLevelEnum extends EnumType
{
    public const NULL       = null;
    public const SIMPLE     = 'user';
    public const SOCIETY    = 'society';
    public const GROUP      = 'group';

    protected string $name = 'enumRoleLevel';

    protected array $values = [
        self::NULL    => self::SIMPLE,
        self::SIMPLE  => self::SIMPLE,
        self::SOCIETY => self::SOCIETY,
        self::GROUP   => self::GROUP,
    ];
}