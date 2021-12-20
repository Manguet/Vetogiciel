<?php

namespace App\Repository\Patients;

use App\Entity\Patients\Client;
use App\Entity\Structure\Clinic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Client) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Get all Clients ordered by name
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null, ?Clinic $clinic = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.lastname', 'ASC');

        if ($q) {
            $qb
                ->orWhere('s.lastname LIKE :q')
                ->orWhere('s.firstname LIKE :q')
                ->orWhere('s.email LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($clinic) {
            $qb
                ->andWhere(':clinic MEMBER OF s.clinic')
                ->setParameter('clinic', $clinic);
        }

        return $qb->getQuery()->getResult();
    }
}
