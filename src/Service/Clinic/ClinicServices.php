<?php

namespace App\Service\Clinic;

use App\Entity\Structure\Clinic;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClinicServices
{
    private EntityManagerInterface $entityManager;

    /**
     * ClinicServices constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getActualClinic()
    {
        $clinicId = $_COOKIE['selectedClinic'] ?? null;

        if ($clinicId) {

            return $this->entityManager->getRepository(Clinic::class)
                ->find((int)$clinicId);
        }

        return $this->entityManager->getRepository(Clinic::class)
            ->findOneBy([]);
    }
}