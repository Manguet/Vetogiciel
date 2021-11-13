<?php

namespace App\Service\Settings;

use App\Entity\Settings\Configuration;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SettingServices
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function siteTitle(): string
    {
        $title = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => 'site_title']);

        if (!$title) {
            return 'Vetogiciel';
        }

        return $title->getDatas()['values'];
    }

    /**
     * @return string
     */
    public function siteDescription(): string
    {
        $title = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => 'site_description']);

        if (!$title) {
            return 'Nous sommes là pour vous !';
        }

        return $title->getDatas()['values'];
    }
}