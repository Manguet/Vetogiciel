<?php

namespace App\Service\Priority;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class PriorityServices
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PriorityServices constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param null $entity
     *
     * @return int
     */
    public function setPriorityOnCreation($entity = null): int
    {
        if (!$entity) {
            throw new InvalidArgumentException('Merci de renseigner une entité', 400);
        }

        $class = get_class($entity);

        return count($this->entityManager->getRepository($class)->findAll());
    }
}