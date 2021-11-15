<?php

namespace App\DBAL\Types\Structures;

use App\DBAL\EnumType;

/**
 * Enum class for define all type of veterinary
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VeterinaryEnum extends EnumType
{
    public const NULL        = null;
    public const GENERALIST  = 'Généraliste';
    public const SPECIALIST  = 'Spécialiste';
    public const STUDENT     = 'Etudiant';
    public const INTERN      = 'Interne';
    public const ASSISTANT   = 'Assistant';

    /**
     * @var string
     */
    protected $name = 'enumVeterinaryTypes';

    /**
     * @var string[]
     */
    protected $values = [
        self::NULL       => self::NULL,
        self::GENERALIST => self::GENERALIST,
        self::SPECIALIST => self::SPECIALIST,
        self::STUDENT    => self::STUDENT,
        self::INTERN     => self::INTERN,
        self::ASSISTANT  => self::ASSISTANT,
    ];

}