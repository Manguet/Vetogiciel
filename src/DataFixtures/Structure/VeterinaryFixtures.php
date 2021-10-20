<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Veterinary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VeterinaryFixtures extends Fixture implements DependentFixtureInterface
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
        'email'        => 'stephane.bureau@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'Bureau',
        'firstname'    => 'Stéphane',
        'lastname'     => 'Bureau',
        'clinic'       => '',
        'color'        => '#C0392B',
        'number'       => '18465',
        'sector'       => '1',
        'isVerified'   => true,
        'type'         => 'Spécialiste'
    ];

    /**
     * Second veterinary
     */
    public const VETERINARY_TWO = [
        'email'        => 'franck.crouzet@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'Crouzet',
        'firstname'    => 'Franck',
        'lastname'     => 'Crouzet',
        'clinic'       => '',
        'color'        => '#99A3A4',
        'number'       => '10236',
        'sector'       => '0,2',
        'isVerified'   => true,
        'type'         => 'Généraliste',
    ];

    /**
     * Third veterinary
     */
    public const VETERINARY_THREE = [
        'email'        => 'veto.unverified@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'unverified',
        'firstname'    => 'Jeanne',
        'lastname'     => 'Doe',
        'clinic'       => '',
        'color'        => '#2ECC71',
        'number'       => '24861',
        'sector'       => '0,1,2',
        'isVerified'   => false,
        'type'         => 'Assistant'
    ];

    /**
     * Array of veterinarians
     */
    public const VETERINARIANS = [
        self::VETERINARY_ONE,
        self::VETERINARY_TWO,
        self::VETERINARY_THREE,
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

                if (0 === strpos($setField, 'sector')) {

                    $setterType = 'add';

                    $values = explode(',', $value);

                    foreach ($values as $sector) {

                        $value = $this->getReference('sector_' . $sector);

                        $veterinary->{$setterType . ucfirst($setField)}($value);
                    }

                } else {

                    $veterinary->{$setterType . ucfirst($setField)}($value);
                }
            }

            $this->addReference('veterinary_' . $key, $veterinary);

            $manager->persist($veterinary);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            SectorFixtures::class,
        ];
    }
}