<?php

namespace App\DataFixtures\Patients;

use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Patients\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * First comment
     */
    public const COMMENT_ONE = [
        'title'       => 'VIP',
        'description' => 'Client très sympa et ancien ASV',
        'color'       => '#2ECC71',
    ];

    /**
     * Second comment
     */
    public const COMMENT_TWO = [
        'title'       => 'Client difficile',
        'description' => 'Client à faire attention',
        'color'       => '#F5B041',
    ];

    /**
     * Third comment
     */
    public const COMMENT_THREE = [
        'title'       => 'Animal difficile',
        'description' => 'Mettre une muselière à l\'arrivée',
        'color'       => '#E74C3C',
    ];


    /**
     * Comments array
     */
    public const COMMENTS = [
        self::COMMENT_ONE,
        self::COMMENT_TWO,
        self::COMMENT_THREE,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::COMMENTS as $key => $COMMENT) {

            $comment = new Comment();

            foreach ($COMMENT as $setField => $value) {

                $comment->{'set' . ucfirst($setField)}($value);
            }

            $this->addReference('comment_' . $key, $comment);
            $comment->setCreatedBy($this->getReference('veterinary_0'));

            $manager->persist($comment);
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