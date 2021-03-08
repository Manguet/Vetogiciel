<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Vat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VatFixtures extends Fixture
{
    /**
     * First vat
     */
    public const VAT_ONE = [
        'title' => '5.5',
        'code'  => '5.5',
        'value' => 5.5,
    ];

    /**
     * Second vat
     */
    public const VAT_TWO = [
        'title' => '10',
        'code'  => '10',
        'value' => 10,
    ];

    /**
     * Third vat
     */
    public const VAT_THREE = [
        'title' => '20',
        'code'  => '20',
        'value' => 20,
    ];

    /**
     * const array of vats
     */
    public const VATS = [
        self::VAT_ONE,
        self::VAT_TWO,
        self::VAT_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::VATS as $key => $VAT) {

            $vat = new Vat();

            foreach ($VAT as $setField => $value) {

                $vat->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('vat_' . $key, $vat);

            $manager->persist($vat);
        }

        $manager->flush();
    }
}