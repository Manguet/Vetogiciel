<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class EmployeeFixtures extends Fixture implements DependentFixtureInterface
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
     * First client
     */
    public const EMPLOYEE_ONE = [
        'email'        => 'john@gmail.com',
        'roles'        => ['ROLE_USER'],
        'password'     => 'johnDo',
        'firstname'    => 'John',
        'lastname'     => 'Do',
        'isManager'    => false,
        'sector'       => '0',
        'isVerified'   => true,
    ];

    /**
     * Second client
     */
    public const EMPLOYEE_TWO = [
        'email'        => 'unVerified@yahoo.fr',
        'roles'        => ['ROLE_USER'],
        'password'     => 'unverified',
        'firstname'    => 'Jean',
        'lastname'     => 'Fiche',
        'isManager'    => false,
        'isVerified'   => false,
    ];

    /**
     * Third Client
     */
    public const EMPLOYEE_THREE = [
        'email'        => 'armelle@hotmail.fr',
        'roles'        => ['ROLE_MANAGER'],
        'password'     => 'armelleDoucet',
        'firstname'    => 'Armelle',
        'lastname'     => 'Doucet',
        'isManager'    => true,
        'sector'       => '0,1,2',
        'isVerified'   => true,
    ];

    public const EMPLOYEES = [
        self::EMPLOYEE_ONE,
        self::EMPLOYEE_TWO,
        self::EMPLOYEE_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::EMPLOYEES as $key => $EMPLOYEE) {

            $employee = new Employee();

            foreach ($EMPLOYEE as $setField => $value) {

                $setterType = 'set';

                if (0 === strpos($setField, 'password')) {

                    $value = $this->encoderServices->encodePassword($employee, $value);
                }

                if (0 === strpos($setField, 'sector')) {

                    $setterType = 'add';

                    $values = explode(',', $value);

                    foreach ($values as $sector) {

                        $value = $this->getReference('sector_' . $sector);

                        $employee->{$setterType . ucfirst($setField)}($value);
                    }

                } else {

                    $employee->{$setterType . ucfirst($setField)}($value);
                }
            }

            $manager->persist($employee);
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