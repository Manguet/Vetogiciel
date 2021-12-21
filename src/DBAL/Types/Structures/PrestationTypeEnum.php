<?php

namespace App\DBAL\Types\Structures;

use App\DBAL\EnumType;

/**
 * Enum class for define all type of prestation
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class PrestationTypeEnum extends EnumType
{
    public const PRESTATION  = 'prestation';
    public const MEDECINE    = 'medecine';
    public const FOOD        = 'food';
    public const OTHERS      = 'other';

    protected string $name = 'enumPrestationTypes';

    protected array $values = [
        self::PRESTATION => self::PRESTATION,
        self::MEDECINE   => self::MEDECINE,
        self::FOOD       => self::FOOD,
        self::OTHERS     => self::OTHERS,
    ];
}
