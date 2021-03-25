<?php

namespace App\Security\Login;

use App\Entity\Patients\Client;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Specific User provider for login checker
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
final class UserProvider implements UserProviderInterface
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
     * @param $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        return $this->findUsername($username);
    }

    /**
     * @param string $username
     *
     * @return mixed
     */
    private function findUsername(string $username)
    {
        $user = $this->entityManager->getRepository(Client::class)
            ->findOneBy(['email' => $username]);

        if (null === $user) {
            $user = $this->entityManager->getRepository(Employee::class)
                ->findOneBy(['email' => $username]);
        }

        if (null === $user) {
            $user = $this->entityManager->getRepository(Veterinary::class)
                ->findOneBy(['email' => $username]);
        }

        if (null === $user) {
            throw new UsernameNotFoundException(
                sprintf('L\'utilisateur "%s" ne semble pas exister.', $username)
            );
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', \get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->findUsername($username);
    }

    /**
     * @param $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        if ($class === Client::class || strpos($class, Client::class)) {
            return true;
        }

        if ($class === Employee::class || strpos($class, Employee::class)) {
            return true;
        }

        if ($class === Veterinary::class ||strpos($class, Veterinary::class)) {
            return true;
        }

        return false;
    }
}