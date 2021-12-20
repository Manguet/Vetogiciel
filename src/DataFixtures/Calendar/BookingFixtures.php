<?php

namespace App\DataFixtures\Calendar;

use App\DataFixtures\Patients\ClientFixtures;
use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Calendar\Booking;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First booking
     */
    public const BOOKING_ONE = [
        'beginAt' => 'P1D',
        'endAt'   => 'P1DT30M',
        'title'   => 'Vaccin',
        'color'   => '#5DADE2'
    ];

    /**
     * Second booking
     */
    public const BOOKING_TWO = [
        'beginAt' => 'P1DT1H',
        'endAt'   => 'P1DT1H30M',
        'title'   => 'Vaccin',
        'color'   => '#5DADE2'
    ];

    /**
     * Third booking
     */
    public const BOOKING_THREE = [
        'beginAt' => 'PT1H',
        'endAt'   => 'PT1H30M',
        'title'   => 'Vomissements',
        'color'   => '#2ECC71'
    ];

    /**
     * const array of booking
     */
    public const BOOKINGS = [
        self::BOOKING_ONE,
        self::BOOKING_TWO,
        self::BOOKING_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::BOOKINGS as $key => $BOOKING) {

            $booking = new Booking();

            foreach ($BOOKING as $setField => $value) {

                if (0 === strpos($setField, 'beginAt')) {

                    $date = new DateTime('now');

                    $interval = new DateInterval($value);

                    $value = $date->add($interval);
                }

                if (0 === strpos($setField, 'endAt')) {

                    $date = new DateTime('now');

                    $interval = new DateInterval($value);

                    $value = $date->add($interval);
                }

                $booking->{'set' . ucfirst($setField)}($value);
            }

            $booking
                ->setCreatedBy($this->getReference('veterinary_0'))
                ->setClient($this->getReference('client_' . $key))
                ->setVeterinary($this->getReference('veterinary_0'))
            ;

            $manager->persist($booking);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            VeterinaryAndSectorFixtures::class,
            ClientFixtures::class
        ];
    }
}