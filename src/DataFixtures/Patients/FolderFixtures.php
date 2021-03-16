<?php

namespace App\DataFixtures\Patients;

use App\Entity\Patients\Folder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class FolderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First folder
     */
    public const FOLDER_ONE = [
        'number' => '00000000000000000001',
        'name'   => 'Pongo_folder',
        'animal' => '',
    ];

    /**
     * Second folder
     */
    public const FOLDER_TWO = [
        'number' => '00000000000000000002',
        'name'   => 'Nibu_folder',
        'animal' => '',
    ];

    /**
     * Third folder
     */
    public const FOLDER_THREE = [
        'number' => '00000000000000000003',
        'name'   => 'Freddy_folder',
        'animal' => '',
    ];

    /**
     * const array of folders
     */
    public const FOLDERS = [
        self::FOLDER_ONE,
        self::FOLDER_TWO,
        self::FOLDER_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::FOLDERS as $key => $FOLDER) {

            $folder = new Folder();

            foreach ($FOLDER as $setField => $value) {

                if (0 === strpos($setField, 'animal')) {

                    $value = $this->getReference('animal_' . $key);
                }

                $folder->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('folder_' . $key, $folder);

            $manager->persist($folder);
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
        ];
    }
}