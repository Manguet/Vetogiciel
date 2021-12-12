<?php

namespace App\DBAL\Types\User;

use App\DBAL\EnumType;

/**
 * Enum class for define all type of genders
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class GenderEnum extends EnumType
{
    public const MR     = 'Monsieur';
    public const MME    = 'Madame';
    public const OTHER  = 'Autre';

    protected string $name = 'enumGender';

    protected array $values = [
        self::MR    => self::MR,
        self::MME   => self::MME,
        self::OTHER => self::OTHER,
    ];
}