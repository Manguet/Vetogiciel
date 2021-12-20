<?php

namespace App\Service\Calendar;

use App\Entity\Calendar\Booking;
use App\Entity\Structure\Veterinary;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @author Benjamin Manguet <benjamin;manguet@gmail.com>
 */
class AdminCalendarServices
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DateTime $now
     * @param int $hour
     * @param int $duration
     * @param int $startAt
     * @param string $motif
     * @param Veterinary $veterinary
     *
     * @throws Exception
     */
    public function addTodayEvents(DateTime $now, int $hour, int $duration, int $startAt,
                                   string $motif, Veterinary $veterinary): void
    {
        $dayOfTheWeek = $now->format('w');

        if ($dayOfTheWeek > 5 || $dayOfTheWeek < 1) {
            return;
        }

        $maxTime = clone $now;

        $interval = 'PT' . $duration . 'M';

        if ($duration === 1) {
            $interval = 'PT1H';
        }

        $period = new DatePeriod(
            $now->setTime($hour, $startAt),
            new DateInterval($interval),
            $maxTime->setTime(20, 00)
        );

        foreach ($period as $datePeriod) {

            $event = $this->entityManager->getRepository(Booking::class)
                ->findOneBy([
                    'veterinary' => $veterinary,
                    'beginAt'    => $datePeriod
                ]);

            if (!$event) {
                $endDate = clone $datePeriod;

                $booking = new Booking();
                $booking
                    ->setTitle($motif)
                    ->setVeterinary($veterinary)
                    ->setBeginAt($datePeriod)
                    ->setEndAt($endDate->add(New DateInterval('PT15M')))
                ;

                $this->entityManager->persist($booking);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param DateTime $now
     * @param int $hour
     * @param int $duration
     * @param int $startAt
     * @param string $motif
     * @param Veterinary $veterinary
     *
     * @throws Exception
     */
    public function addWeekEvents(DateTime $now, int $hour,int $duration, int $startAt,
                                  string $motif, Veterinary $veterinary): void
    {
        $today = $now->format('w');
        $i     = $today;

        for ($today; $today <= 5; $today++) {

            if ($today !== $i) {
                $hour = 9;
                $now  = $now->add(new DateInterval('P1D'));
                $now->setTime(9, 0);
            }

            $this->addTodayEvents($now, $hour, $duration, $startAt, $motif, $veterinary);
        }
    }

    /**
     * @param DateTime $now
     * @param int $hour
     * @param int $duration
     * @param int $startAt
     * @param string $motif
     * @param Veterinary $veterinary
     *
     * @throws Exception
     */
    public function addMonthEvents(DateTime $now, int $hour, int $duration,
                                   int $startAt, string $motif, Veterinary $veterinary): void
    {
        $today  = $now->format('d');
        $maxDay = $now->format('t');

        $i = $today;

        for ($today; $today <= $maxDay; $today++) {

            if ($today !== $i) {
                $hour = 9;
                $now  = $now->add(new DateInterval('P1D'));
                $now->setTime(9, 0);
            }

            $this->addTodayEvents($now, $hour, $duration, $startAt, $motif, $veterinary);
        }
    }
}