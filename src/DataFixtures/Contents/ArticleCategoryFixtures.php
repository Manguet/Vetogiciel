<?php

namespace App\DataFixtures\Contents;

use App\DataFixtures\Structure\ClinicFixtures;
use App\DataFixtures\Structure\VeterinaryAndSectorFixtures;
use App\Entity\Contents\ArticleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ArticleCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $articleCategory = new ArticleCategory();

        $articleCategory
            ->setTitle('Informations')
            ->setTitleUrl('information')
            ->setClinic($this->getReference('clinic_0'))
            ->setCreatedBy($this->getReference('veterinary_0'))
        ;

        $this->addReference('articleCategory', $articleCategory);

        $manager->persist($articleCategory);
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            VeterinaryAndSectorFixtures::class,
            ClinicFixtures::class
        ];
    }
}