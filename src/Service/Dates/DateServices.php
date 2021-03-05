<?php

namespace App\Service\Dates;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DateServices
{
    /**
     * @return string
     *
     * @throws Exception
     */
    public function getCurrentDate(): string
    {
        $timeZone = new DateTimeZone('Europe/Paris');

        $date = new DateTime('now', $timeZone);

        return $date->format('d/m/Y H:i:s');
    }

    /**
     * @return DateTime
     *
     * @throws Exception
     */
    public function getCurrentDateObject(): DateTime
    {
        $timeZone = new DateTimeZone('Europe/Paris');

        return new DateTime('now', $timeZone);
    }
}