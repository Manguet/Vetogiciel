<?php

namespace App\DBAL\Types\Structures;

use App\DBAL\EnumType;

/**
 * Enum class for define all type of structure
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class StructureTypeEnum extends EnumType
{
    public const SMALL       = 'Cabinet';
    public const CLINIC      = 'Clinique';
    public const SPECIALISTS = 'Centre de Vétérinaires Spécialistes';
    public const CHV         = 'Centre Hospitalier Vétérinaire';

    protected string $name = 'enumStructureTypes';

    protected array $values = [
        self::SMALL       => self::SMALL,
        self::CLINIC      => self::CLINIC,
        self::SPECIALISTS => self::SPECIALISTS,
        self::CHV         => self::CHV,
    ];
}
