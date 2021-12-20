<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Clinic;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClinicFixtures extends Fixture
{
    /**
     * First clinic
     */
    public const CLINIC_ONE = [
        'name'           => 'Clinique vétérinaire Alliance',
        'nameSlugiffied' => 'clinique-veterinaire-alliance',
        'fiscalDate'     => 2,
        'status'         => 'SARL',
        'address'        => '8, Boulevard Godard',
        'address2'       => '',
        'postalCode'     => '33300',
        'city'           => 'Bordeaux',
        'phone'          => '0556391548',
        'phone2'         => '0676721883',
        'email'          => 'accueil@alliance.fr',
        'siren'          => '329233928',
        'siret'          => '32923392800046',
        'type'           => 'Clinique',
    ];

    /**
     * Second clinic
     */
    public const CLINIC_TWO = [
        'name'           => 'Cabinet du bois pigeonnier',
        'nameSlugiffied' => 'cabinet-du-bois-pigeonnier',
        'fiscalDate'     => 8,
        'status'         => 'SELARL',
        'address'        => '1, rue de la plage',
        'address2'       => 'Place du centre',
        'postalCode'     => '85640',
        'city'           => 'Saint jean de monts',
        'phone'          => '0304087964',
        'phone2'         => null,
        'email'          => 'cabinetduboispigeonnier@gmail.com',
        'siren'          => '129833227',
        'siret'          => '12983322700031',
        'type'           => 'Cabinet',
    ];

    /**
     * const array of clinics
     */
    public const CLINICS = [
        self::CLINIC_ONE,
        self::CLINIC_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::CLINICS as $key => $CLINIC) {

            $clinic = new Clinic();

            foreach ($CLINIC as $setField => $value) {

                if (0 === strpos($setField, 'fiscalDate')) {

                    $date = new DateTime('now');

                    $value = $date->modify('-' . $value . ' month');
                }

                $clinic->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('clinic_' . $key, $clinic);

            $manager->persist($clinic);
        }

        $manager->flush();
    }
}