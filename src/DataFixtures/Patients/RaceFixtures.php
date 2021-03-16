<?php

namespace App\DataFixtures\Patients;

use App\Entity\Patients\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RaceFixtures extends Fixture
{
    /**
     * First race
     */
    public const RACE_ONE = [
        'name' => 'Chien',
    ];

    /**
     * Second race
     */
    public const RACE_TWO = [
        'name' => 'Chat',
    ];

    /**
     * Third race
     */
    public const RACE_THREE = [
        'name' => 'Cheval',
    ];

    /**
     * Animals array
     */
    public const RACES_DATA = [
        self::RACE_ONE,
        self::RACE_TWO,
        self::RACE_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::RACES_DATA as $key => $RACE) {

            $race = new Race();

            foreach ($RACE as $setField => $value) {

                $race->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('race_' . $key, $race);

            $manager->persist($race);
        }

        $manager->flush();
    }
}