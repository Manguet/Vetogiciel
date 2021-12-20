<?php

namespace App\DataFixtures\Structure;

use App\Entity\Structure\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First note
     */
    public const NOTE_ONE = [
        'title'       => 'Commandes',
        'description' => '<ul>A commander : 
                                <li>Milbemax</li>
                                <li>Drontal</li>
                            </ul>
                            ',
    ];

    /**
     * Second note
     */
    public const NOTE_TWO = [
        'title'       => 'Hygiène',
        'description' => 'Désynfecter la chirurgie le 01/01/2022 (LEPTO)',
    ];

    /**
     * Third note
     */
    public const NOTE_THREE = [
        'title'       => 'Entretiens',
        'description' => 'Relancer les candidats',
    ];

    /**
     * const array of folders
     */
    public const NOTES = [
        self::NOTE_ONE,
        self::NOTE_TWO,
        self::NOTE_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::NOTES as $key => $NOTE) {

            $note = new Note();

            foreach ($NOTE as $setField => $value) {

                $note->{'set' . ucfirst($setField)}($value);
            }

            $note->setCreatedBy($this->getReference('veterinary_0'));

            $manager->persist($note);
        }

        $manager->flush();
    }


    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            VeterinaryAndSectorFixtures::class,
        ];
    }
}