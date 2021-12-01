<?php

namespace App\Repository\Settings;

use App\Entity\Settings\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * Get all Role ordered by name
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC');

        if ($q) {
            $qb
                ->where('r.name LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
