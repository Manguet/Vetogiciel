<?php

namespace App\DBAL\Types\Configuration;

use App\DBAL\EnumType;

/**
 * Enum class for define all type of configurations
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ConfigurationTypeEnum extends EnumType
{
    public const GENERAL     = 'general';
    public const ADVANCED    = 'advanced';
    public const SPECIFIC    = 'specific';

    /**
     * @var string
     */
    protected $name = 'enumConfigurationTypes';

    /**
     * @var string[]
     */
    protected $values = [
        self::GENERAL  => self::GENERAL,
        self::ADVANCED => self::ADVANCED,
        self::SPECIFIC => self::SPECIFIC,
    ];
}
