<?php

namespace App\DataFixtures\Patients;

use App\Entity\Patients\Species;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SpeciesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First race
     */
    public const SPECIE_ONE = [
        'name' => 'Labrador',
        'race' => null,
    ];

    /**
     * Second race
     */
    public const SPECIE_TWO = [
        'name' => 'Européen',
        'race' => null,
    ];

    /**
     * Third race
     */
    public const SPECIE_THREE = [
        'name' => 'Pur sang arabe',
        'race' => null,
    ];

    /**
     * Sêcies array
     */
    public const SPECIES_DATA = [
        self::SPECIE_ONE,
        self::SPECIE_TWO,
        self::SPECIE_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::SPECIES_DATA as $key => $SPECIE) {

            $specie = new Species();

            foreach ($SPECIE as $setField => $value) {

                if (0 === strpos($setField, 'race')) {

                    $value = $this->getReference('race_' . $key);
                }

                $specie->{'set' . ucfirst($setField)}($value);
            }

            $specie->setCreatedBy($this->getReference('veterinary_0'));

            $this->addReference('species_' . $key, $specie);

            $manager->persist($specie);
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
        ];
    }
}