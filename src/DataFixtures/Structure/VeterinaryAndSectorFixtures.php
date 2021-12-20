<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VeterinaryAndSectorFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoderServices;

    /**
     * EmployeeFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $encoderServices
     */
    public function __construct(UserPasswordEncoderInterface $encoderServices)
    {
        $this->encoderServices = $encoderServices;
    }

    /**
     * First veterinary
     */
    public const VETERINARY_ONE = [
        'email'              => 'stephane.bureau@gmail.com',
        'roles'              => ['ROLE_DIRECTOR'],
        'password'           => 'Bureau',
        'firstname'          => 'Stéphane',
        'lastname'           => 'Bureau',
        'clinic'             => '',
        'color'              => '#C0392B',
        'number'             => '18465',
        'isVerified'         => true,
        'type'               => 'Spécialiste',
        'fullNameSlugiffied' => 'stephane-bureau'
    ];

    /**
     * Second veterinary
     */
    public const VETERINARY_TWO = [
        'email'              => 'franck.crouzet@gmail.com',
        'roles'              => ['ROLE_USER'],
        'password'           => 'Crouzet',
        'firstname'          => 'Franck',
        'lastname'           => 'Crouzet',
        'clinic'             => '',
        'color'              => '#99A3A4',
        'number'             => '10236',
        'isVerified'         => true,
        'type'               => 'Généraliste',
        'fullNameSlugiffied' => 'franck-crouzet'
    ];

    /**
     * Third veterinary
     */
    public const VETERINARY_THREE = [
        'email'              => 'veto.unverified@gmail.com',
        'roles'              => ['ROLE_USER'],
        'password'           => 'unverified',
        'firstname'          => 'Jeanne',
        'lastname'           => 'Doe',
        'clinic'             => '',
        'color'              => '#2ECC71',
        'number'             => '24861',
        'isVerified'         => false,
        'type'               => 'Assistant',
        'fullNameSlugiffied' => 'jeanne-doe'
    ];

    /**
     * Array of veterinarians
     */
    public const VETERINARIANS = [
        self::VETERINARY_ONE,
        self::VETERINARY_TWO,
        self::VETERINARY_THREE,
    ];

    public const SECTOR = [
        0 => [
            'name'        => 'Généraliste',
            'description' => 'Médecine vétérinaire pour les soins de tous les jours.',
            ],
        1 => [
            'name'        => 'Chirurgie',
            'description' => 'Chirurgies de convenance.',
        ],
        2 => [
            'name'        => 'Urgences',
            'description' => 'Gestion des urgences.',
        ]
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::VETERINARIANS as $key => $VETERINARY) {

            $veterinary = new Veterinary();

            foreach ($VETERINARY as $setField => $value) {

                $setterType = 'set';

                if (0 === strpos($setField, 'password')) {

                    $value = $this->encoderServices->encodePassword($veterinary, $value);
                }

                if (0 === strpos($setField, 'clinic')) {

                    $value = $this->getReference('clinic_0');
                }

                $veterinary->{$setterType . ucfirst($setField)}($value);

            }

            $this->addReference('veterinary_' . $key, $veterinary);

            $sector = new Sector();

            foreach (self::SECTOR[$key] as $setField => $value) {
                $sector->{'set' . ucfirst($setField)}($value);
            }

            $sector->setCreatedBy($veterinary);
            $veterinary
                ->addSector($sector)
                ->setCreatedBy($veterinary);

            $this->addReference('sector_' . $key, $sector);

            $manager->persist($sector);
            $manager->persist($veterinary);
        }

        $manager->flush();
    }
}