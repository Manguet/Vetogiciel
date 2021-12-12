<?php

namespace App\Service\Settings;

use App\Entity\Settings\Configuration;
use App\Entity\Structure\Clinic;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SettingServices
{
    private EntityManagerInterface $entityManager;

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

        return $title->getDatas()['values'] ?? 'Vetogiciel';
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

        return $title->getDatas()['values'] ?? 'Nous sommes là pour vous !';
    }

    /**
     * @return bool
     */
    public function isBreadcrumbsActivated(): bool
    {
        $isActivated = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => 'activate_breadcrumbs']);

        if (!$isActivated) {
            return true;
        }

        return $isActivated->getDatas()['values'] ?? false;
    }

    /**
     * @param Clinic $clinic
     *
     * @return null|string
     */
    public function getPhotograph(Clinic $clinic): ?string
    {
        $photograph = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy([
                'name'   => 'photo_author_' . $clinic->getId(),
            ]);

        if (!$photograph) {
            return null;
        }

        return $photograph->getDatas()['values'] ?? null;
    }

    /**
     * @param Clinic $clinic
     *
     * @return string|null
     */
    public function getPhotographSite(Clinic $clinic): ?string
    {
        $photographSite = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy([
                'name'   => 'photograph_' . $clinic->getId(),
            ]);

        if (!$photographSite) {
            return null;
        }

        return $photographSite->getDatas()['values'] ?? null;
    }
}