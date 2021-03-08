<?php

namespace App\DataFixtures\External;

use App\Entity\Contents\Article;
use App\Entity\External\Laboratory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class LaboratoryFixtures extends Fixture
{
    /**
     * First laboratory
     */
    public const LABORATORY_ONE = [
        'name'       => 'MERIAL',
        'address'    => '10, rue des poneys',
        'address2'   => 'Chemin long',
        'postalCode' => 33700,
        'city'       => 'Mérignac',
        'phone'      => '0556374613',
        'phone2'     => '0646879702',
        'email'      => 'contact@merial.fr',
    ];

    /**
     * Second laboratory
     */
    public const LABORATORY_TWO = [
        'name'       => 'MSD',
        'address'    => '50, Rue des cadres',
        'postalCode' => 51100,
        'city'       => 'Reims',
        'phone'      => '0148632579',
        'email'      => 'contact@msd.fr',
    ];

    /**
     * const array of laboratories
     */
    public const LABORATORIES = [
        self::LABORATORY_ONE,
        self::LABORATORY_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::LABORATORIES as $key => $LABORATORY) {

            $laboratory = new Laboratory();

            foreach ($LABORATORY as $setField => $value) {

                $laboratory->{'set' . ucfirst($setField)}($value);
            }

            $manager->persist($laboratory);
        }

        $manager->flush();
    }
}