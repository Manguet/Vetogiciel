<?php

namespace App\DataFixtures\Patients;

use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Patients\Consultation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ConsultationFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First consultation
     */
    public const CONSULTATION_ONE = [
        'title'       => 'Piroplasmose',
        'description' => 'EG : abattu, Piroplasmose',
        'folder'      => '',
        'veterinary'  => '1',
        'prestation'  => '2',
    ];

    /**
     * Second consultation
     */
    public const CONSULTATION_TWO = [
        'title'       => 'Castration',
        'description' => 'EG: bon, Castration chirurgicale',
        'folder'      => '',
        'veterinary'  => '0',
        'prestation'  => '0,2',
    ];

    /**
     * const array of laboratories
     */
    public const CONSULTATIONS = [
        self::CONSULTATION_ONE,
        self::CONSULTATION_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::CONSULTATIONS as $key => $CONSULTATION) {

            $consultation = new Consultation();

            foreach ($CONSULTATION as $setField => $value) {

                $setterType = 'set';

                if (0 === strpos($setField, 'folder')) {

                    $value = $this->getReference('folder_0');
                }

                if (0 === strpos($setField, 'veterinary')) {

                    $value = $this->getReference('veterinary_' . $value);
                }


                if (0 === strpos($setField, 'prestation')) {

                    $setterType = 'add';

                    $values = explode(',', $value);

                    foreach ($values as $prestation) {

                        $value = $this->getReference('prestation_' . $prestation);

                        $consultation->{$setterType . ucfirst($setField)}($value);
                    }

                } else {

                    $consultation->{$setterType . ucfirst($setField)}($value);

                }
            }

            $consultation->setCreatedBy($this->getReference('veterinary_' . $key));

            $manager->persist($consultation);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            FolderFixtures::class,
            VeterinaryAndSectorFixtures::class,
        ];
    }
}