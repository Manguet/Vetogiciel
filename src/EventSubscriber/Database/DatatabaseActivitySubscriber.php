<?php

namespace App\EventSubscriber\Database;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DatatabaseActivitySubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->addCreatedBy($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->addCreatedBy($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function addCreatedBy(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!method_exists($entity, 'setCreatedBy') && null !== $entity->getCreatedBy()) {
            return;
        }

        $user = $this->security->getUser();

        $entity->setCreatedBy($user);
    }
}