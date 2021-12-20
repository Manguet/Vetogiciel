<?php

namespace App\DataFixtures\Patients;

use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Patients\Animal;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AnimalFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First animal alive
     */
    public const ANIMAL_ONE = [
        'name'           => 'Pongo',
        'age'            => 10,
        'color'          => 'Sable',
        'transponder'    => '250269000000000',
        'tatoo'          => '2GRE875',
        'isLof'          => true,
        'isInsured'      => true,
        'isAlive'        => true,
        'race'           => null,
        'species'        => null,
        'comment'        => null,
        'client'         => null,
        'waitingRoom'    => null,
        'birthdate'      => null,
        'idLocalization' => 'Gouttière jugulaire gauche',
    ];

    /**
     * Second animal
     */
    public const ANIMAL_TWO = [
        'name'           => 'Nibu',
        'age'            => 4,
        'color'          => 'Noir et Blanc',
        'transponder'    => '250269000111222',
        'tatoo'          => '',
        'isLof'          => false,
        'isInsured'      => false,
        'isAlive'        => true,
        'race'           => null,
        'species'        => null,
        'client'         => null,
        'waitingRoom'    => null,
        'birthdate'      => null,
        'idLocalization' => 'Gouttière jugulaire gauche',
    ];

    /**
     * Third animal dead
     */
    public const ANIMAL_THREE = [
        'name'           => 'Freddy',
        'age'            => 12,
        'color'          => 'marron',
        'transponder'    => '269250123479874',
        'tatoo'          => '',
        'isLof'          => false,
        'isInsured'      => false,
        'isAlive'        => false,
        'race'           => null,
        'species'        => null,
        'client'         => null,
        'waitingRoom'    => null,
        'birthdate'      => null,
        'idLocalization' => 'Gouttière jugulaire gauche',
    ];

    /**
     * Animals array
     */
    public const ANIMALS_DATA = [
        self::ANIMAL_ONE,
        self::ANIMAL_TWO,
        self::ANIMAL_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::ANIMALS_DATA as $key => $ANIMAL) {

            $animal = new Animal();

            foreach ($ANIMAL as $setField => $value) {

                if (0 === strpos($setField, 'race')) {

                    $value = $this->getReference('race_' . $key);
                                    }

                if (0 === strpos($setField, 'species')) {

                    $value = $this->getReference('species_' . $key);
                }

                if (0 === strpos($setField, 'birthdate')) {

                    $today = new DateTime('now');

                    $value = $today->modify('-' . $ANIMAL['age'] . ' year');
                }

                if (0 === strpos($setField, 'comment')) {

                    $value = $this->getReference('comment_2');
                }

                $animal->{'set' . ucfirst($setField)}($value);
            }

            $animal->setCreatedBy($this->getReference('veterinary_0'));

            $this->addReference('animal_' . $key, $animal);

            $manager->persist($animal);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            RaceFixtures::class,
            SpeciesFixtures::class,
            CommentFixtures::class,
            VeterinaryAndSectorFixtures::class
        ];
    }
}