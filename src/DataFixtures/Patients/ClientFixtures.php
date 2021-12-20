<?php

namespace App\DataFixtures\Patients;

use App\DataFixtures\Structure\ClinicFixtures;
use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Patients\Client;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoderServices;

    /**
     * ClientFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $encoderServices
     */
    public function __construct(UserPasswordEncoderInterface $encoderServices)
    {
        $this->encoderServices = $encoderServices;
    }

    /**
     * First client
     */
    public const CLIENT_ONE = [
        'email'        => 'admin@admin.fr',
        'roles'        => ['ROLE_USER'],
        'password'     => 'admin',
        'firstname'    => 'admin',
        'lastname'     => 'admin',
        'address'      => '01, Rue des admins',
        'address2'     => '',
        'postalCode'   => '00001',
        'city'         => 'AdminVille',
        'phoneNumber'  => '0505050505',
        'phoneNumber2' => '0606060606',
        'isInDebt'     => false,
        'animal'       => '',
        'lastVisit'    => null,
        'clinic'       => null,
        'isVerified'   => true,
    ];

    /**
     * Second client
     */
    public const CLIENT_TWO = [
        'email'        => 'benjamin.manguet@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'sobeck0099',
        'firstname'    => 'Benjamin',
        'lastname'     => 'Manguet',
        'address'      => '44, rue de la garenne',
        'address2'     => 'Appartement B002',
        'postalCode'   => '33600',
        'city'         => 'Pessac',
        'phoneNumber'  => null,
        'phoneNumber2' => '0637505098',
        'comment'      => '',
        'isInDebt'     => false,
        'animal'       => '',
        'lastVisit'    => 2,
        'clinic'       => null,
        'isVerified'   => true,
    ];

    /**
     * Third Client
     */
    public const CLIENT_THREE = [
        'email'        => 'jonathan.manguet@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'Anubis0099',
        'firstname'    => 'Jonathan',
        'lastname'     => 'Manguet',
        'address'      => '1bis, rue des près carrés',
        'address2'     => '',
        'postalCode'   => '17220',
        'city'         => 'Sainte Soulle',
        'phoneNumber'  => '0546370104',
        'phoneNumber2' => '0678794321',
        'comment'      => '',
        'isInDebt'     => true,
        'animal'       => '',
        'lastVisit'    => 180,
        'clinic'       => null,
        'isVerified'   => false,
    ];

    public const CLIENTS = [
        self::CLIENT_ONE,
        self::CLIENT_TWO,
        self::CLIENT_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::CLIENTS as $key => $CLIENT) {

            $client = new Client();

            foreach ($CLIENT as $setField => $value) {

                $setterType = 'set';

                if (0 === strpos($setField, 'password')) {

                    $value = $this->encoderServices->encodePassword($client, $value);
                }

                if (0 === strpos($setField, 'animal')) {

                    $setterType = 'add';

                    $value = $this->getReference('animal_' . $key);
                }

                if (0 === strpos($setField, 'lastVisit')) {

                    if ($value) {

                        $today = new DateTime('now');

                        $value = $today->modify('-' . $value . ' day');
                    }
                }

                if (0 === strpos($setField, 'clinic')) {

                    $setterType = 'add';

                    $value = $this->getReference('clinic_0');
                }

                if (0 === strpos($setField, 'comment')) {

                    $value = $this->getReference('comment_' . ($key - 1));
                }

                $client->{$setterType . ucfirst($setField)}($value);
            }

            $client->setCreatedBy($this->getReference('veterinary_0'));

            $this->addReference('client_' . $key, $client);

            $manager->persist($client);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            AnimalFixtures::class,
            ClinicFixtures::class,
            CommentFixtures::class,
            VeterinaryAndSectorFixtures::class
        ];
    }
}