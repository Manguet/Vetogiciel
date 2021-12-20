<?php

namespace App\DataFixtures\Structure;

use App\DataFixtures\Patients\FolderFixtures;
use App\Entity\Structure\Bill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class BillFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First bill
     */
    public const BILL_ONE = [
        'number'   => '00000000000000000001',
        'priceHT'  => 12.70,
        'priceTTC' => 11.43,
        'folder'   => '',
    ];

    /**
     * Second bill
     */
    public const BILL_TWO = [
        'number'   => '00000000000000000002',
        'priceHT'  => 172.70,
        'priceTTC' => 187.43,
        'folder'   => '',
    ];

    /**
     * const array of bills
     */
    public const BILLS = [
        self::BILL_ONE,
        self::BILL_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::BILLS as $key => $BILL) {

            $bill = new Bill();

            foreach ($BILL as $setField => $value) {

                if (0 === strpos($setField, 'folder')) {

                    $value = $this->getReference('folder_0');

                }

                $bill->{'set' . ucfirst($setField)}($value);
            }

            $bill->setCreatedBy($this->getReference('veterinary_' . $key));

            $this->addReference('bill_' . $key, $bill);

            $manager->persist($bill);
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
            VeterinaryAndSectorFixtures::class
        ];
    }
}