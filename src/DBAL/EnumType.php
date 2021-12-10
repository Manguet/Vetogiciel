<?php

namespace App\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Class enum for mysql bdd support
 */
abstract class EnumType extends Type
{
    protected string $name;

    protected array $values = [];

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = array_map(static function($val) { return "'" . $val . "'"; }, $this->values);

        return "ENUM(" . implode(", ", $values) . ")";
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            $value = $this->getValues()[''];
        }

        if (!in_array($value, $this->values, true)) {
            throw new InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}