<?php

namespace App\Service\Dates;

use App\Entity\Patients\Animal;
use DateInterval;
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

    /**
     * @param Animal $animal
     *
     * @throws Exception
     *
     * @return void
     */
    public function calculateAges(Animal $animal): void
    {
        if (null !== $animal->getBirthdate()) {
            $actualDate = $this->getCurrentDateObject();

            $interval = $actualDate->diff($animal->getBirthdate())->y;
            $animal->setAge($interval);

        } elseif (null !== $animal->getAge()) {
            $actualDate = $this->getCurrentDateObject();

            $interval = new DateInterval('P' . $animal->getAge() . 'Y');
            $date = $actualDate->sub($interval);

            $animal->setBirthdate($date);
        }
    }
}