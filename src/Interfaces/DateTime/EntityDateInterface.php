<?php

namespace App\Interfaces\DateTime;

use DateTime;
use DateTimeImmutable;

/**
 * Interface to add getter and setters in entity
 * DateCreation and DateUpdate
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
Interface EntityDateInterface
{
    /**
     * @return DateTimeImmutable
     */
    public function getDateCreation(): DateTimeImmutable;

    /**
     * @return DateTime|null
     */
    public function getDateUpdate(): string;

    /**
     * @return void
     */
    public function prePersist(): void;
}