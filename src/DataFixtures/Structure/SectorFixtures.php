<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SectorFixtures extends Fixture
{
    /**
     * First sector
     */
    public const SECTOR_ONE = [
        'name'        => 'Généraliste',
        'description' => 'Médecine vétérinaire pour les soins de tous les jours.',
    ];

    /**
     * Second sector
     */
    public const SECTOR_TWO = [
        'name'        => 'Chirurgie',
        'description' => 'Chirurgies de convenance.',
    ];

    /**
     * Third Sector
     */
    public const SECTOR_THREE = [
        'name'        => 'Urgences',
        'description' => 'Gestion des urgences.',
    ];

    /**
     * array of sectors
     */
    public const SECTORS = [
        self::SECTOR_ONE,
        self::SECTOR_TWO,
        self::SECTOR_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::SECTORS as $key => $SECTOR) {

            $sector = new Sector();

            foreach ($SECTOR as $setField => $value) {

                $sector->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('sector_' . $key, $sector);

            $manager->persist($sector);
        }

        $manager->flush();
    }
}