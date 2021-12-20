<?php

namespace App\Repository\Settings;

use App\Entity\Settings\Configuration;
use App\Entity\Structure\Clinic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    /**
     * @param string $configurationType
     *
     * @return int|mixed|string
     */
    public function findByConfigurationInOrder(string $configurationType)
    {
        return $this->createQueryBuilder('c')
            ->where('c.configurationType = :type')
            ->setParameters([
                'type' => $configurationType
            ])
            ->orderBy('c.onglet', 'ASC')
            ->addOrderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param Clinic $clinic
     *
     * @return int|mixed|string
     *
     * @throws QueryException
     */
    public function getClinicCalendarSettings(Clinic $clinic)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :day')
            ->orWhere('c.name LIKE :saturday')
            ->orWhere('c.name LIKE :sunday')
            ->andWhere('c.name LIKE :clinicId')
            ->setParameters([
                'day'      => 'days_%',
                'saturday' => 'saturday_%',
                'sunday'   => 'sunday_%',
                'clinicId' => '%_' . $clinic->getId()
            ])
            ->indexBy('c', 'c.name')
            ->getQuery()
            ->getResult()
        ;
    }
}