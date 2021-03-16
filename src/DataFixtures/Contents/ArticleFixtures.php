<?php

namespace App\DataFixtures\Contents;

use App\Entity\Contents\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ArticleFixtures extends Fixture
{
    /**
     * First article
     */
    public const ARTICLE_ONE = [
        'title'       => 'Coronavirus',
        'description' => 'Attention, le coronavirus humain n\'a rien à voir a celui des chats !',
        'isActivated' => true,
        'priority'    => 0,
    ];

    /**
     * Second article
     */
    public const ARTICLE_TWO = [
        'title'       => 'Fermeture annuelle',
        'description' => 'La clinique sera exceptionnellement fermé pour l\'entretien des locaux le 1er décembre',
        'isActivated' => true,
        'priority'    => 1,
    ];

    /**
     * const array of articles
     */
    public const ARTICLES = [
        self::ARTICLE_ONE,
        self::ARTICLE_TWO,
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::ARTICLES as $key => $ARTICLE) {

            $article = new Article();

            foreach ($ARTICLE as $setField => $value) {

                $article->{'set' . ucfirst($setField)}($value);
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}