<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Prestation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class PrestationFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First prestation
     */
    public const PRESTATION_ONE = [
        'title'        => 'Castration chien',
        'description'  => 'Castration Chirurgicale Chien',
        'code'         => 'Cast_Chir_Cn',
        'PriceHT'      => 160.00,
        'PriceTTC'     => 176.00,
        'vat'          => '1',
        'type'         => 'prestation',
    ];

    /**
     * Second prestation
     */
    public const PRESTATION_TWO = [
        'title'        => 'Castration chat',
        'description'  => 'Castration Chirurgicale Chat',
        'code'         => 'Cast_Chir_Ct',
        'PriceHT'      => 70.00,
        'PriceTTC'     => 77.00,
        'vat'          => '1',
        'type'         => 'prestation',
    ];

    /**
     * Third prestation
     */
    public const PRESTATION_THREE = [
        'title'        => 'Amoxiciline',
        'description'  => 'Injection antibiotique, injection non comprise',
        'code'         => 'AMX_INJ',
        'quantity'     => 80,
        'PriceHT'      => 13.00,
        'PriceTTC'     => 13.65,
        'reduction'    => 10.00,
        'vat'          => '0',
        'type'         => 'medecine',
    ];

    /**
     * const array of prestations
     */
    public const PRESTATIONS = [
        self::PRESTATION_ONE,
        self::PRESTATION_TWO,
        self::PRESTATION_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::PRESTATIONS as $key => $PRESTATION) {

            $prestation = new Prestation();

            foreach ($PRESTATION as $setField => $value) {

                if (0 === strpos($setField, 'vat')) {

                    $value = $this->getReference('vat_' . $value);
                }

                $prestation->{'set' . ucfirst($setField)}($value);
            }

            $prestation->setCreatedBy($this->getReference('veterinary_' . $key));

            $this->addReference('prestation_' . $key, $prestation);

            $manager->persist($prestation);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            VatFixtures::class,
            VeterinaryAndSectorFixtures::class
        ];
    }
}