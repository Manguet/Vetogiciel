<?php

namespace App\DataFixtures\Structure;

use App\DataFixtures\Patients\AnimalFixtures;
use App\Entity\Structure\WaitingRoom;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class WaitingRoomFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First room
     */
    public const ROOM_ONE = [
        'name'     => 'Salle canins',
        'capacity' => 10,
    ];

    /**
     * Second room
     */
    public const ROOM_TWO = [
        'name'     => 'Salle félins',
        'capacity' => 6,
        'animal'   => '',
    ];

    /**
     * const array of rooms
     */
    public const ROOMS = [
        self::ROOM_ONE,
        self::ROOM_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::ROOMS as $key => $ROOM) {

            $room = new WaitingRoom();

            foreach ($ROOM as $setField => $value) {

                $setterType = 'set';

                if (0 === strpos($setField, 'animal')) {

                    $setterType = 'add';

                    $value = $this->getReference('animal_1');
                }

                $room->{$setterType . ucfirst($setField)}($value);
            }

            $manager->persist($room);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            AnimalFixtures::class,
        ];
    }
}