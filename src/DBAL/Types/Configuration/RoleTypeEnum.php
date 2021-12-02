<?php

namespace App\DBAL\Types\Configuration;

use App\DBAL\EnumType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RoleTypeEnum extends EnumType
{
    public const SYSTEM = 'system';
    public const CUSTOM = 'custom';

    /**
     * @var string
     */
    protected $name = 'enumRoleType';

    /**
     * @var string[]
     */
    protected $values = [
        self::SYSTEM => self::SYSTEM,
        self::CUSTOM => self::CUSTOM,
    ];
}