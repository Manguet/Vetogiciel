<?php

namespace App\Security\Voters;

use App\Entity\Settings\Role;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminAccessVoter extends Voter
{
    /**
     * @var string regex for access
     */
    public const START_WITH_ACCESS = '/^ADMIN_/';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $allRoles = [];

    /**
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->kernel        = $kernel;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!preg_match(self::START_WITH_ACCESS, $attribute)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $roles = $user->getRoles();

        /**
         * @var Role $role
         */
        $role = $this->entityManager->getRepository(Role::class)
            ->findOneBy(['name' => $roles[0]]);

        if (!$role instanceof Role || empty($role->getAuthorizations())) {
            return false;
        }

        $this->getAllAuthorizations($role);

        $entityExploded = explode('_', $attribute);
        $entity = $entityExploded[1];

        if (in_array('ADMIN_FULL_ACCESS', $this->allRoles, true)
            || in_array('ADMIN_' . $entity . '_MANAGE', $this->allRoles, true)
            || in_array('ADMIN_' . $entity . '_ACCESS', $this->allRoles, true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Role $role
     *
     * @return void
     */
    private function getAllAuthorizations(Role $role): void
    {
        if (empty($role->getAuthorizations())) {
            return;
        }

        foreach ($role->getAuthorizations() as $authorization) {

            if (!in_array($authorization, $this->allRoles, true)) {
                $this->allRoles[] = $authorization;
            }
        }

        if (!$role->getChildRoles()->isEmpty()) {

            foreach ($role->getChildRoles() as $childRole) {
                $this->getAllAuthorizations($childRole);
            }
        }
    }

//    /**
//     * @param string $authorizationLevel
//     * @param UserInterface $user
//     * @param string $entity
//     *
//     * @return void
//     */
//    private function checkLevelAuthorization(string $authorizationLevel, UserInterface $user, string $entity): void
//    {
//        switch ($authorizationLevel) {
//            case 'group':
//                break;
//            case 'society':
//                $this->addConstraintOnSociety($user, $entity);
//                break;
//            case 'user':
//                $this->addConstrantOnUser($user, $entity);
//                break;
//            default:
//                throw new InvalidArgumentException('Le rôle n\'a pas de niveau de permission', 400);
//        }
//    }

//    private function addConstraintOnSociety(UserInterface $user, string $entityString)
//    {
//        $finder = new Finder();
//        $finder->files()->in($this->kernel->getProjectDir() . '/src/Entity')
//            ->name(ucfirst(strtolower($entityString)) . '.php');
//
//        if (!$finder->hasResults()) {
//            return;
//        }
//
//        $class = null;
//        foreach ($finder as $file) {
//
//            $class = 'App\\' . str_replace(
//                ['.php', '/'],
//                ['', '\\'],
//                $file->getRelativePathname()
//            );
//        }
//
//        if (!$class) {
//            return;
//        }
//
//        $this->entityManager->getFilters()->enable('clinicFilter');
//    }
//
//    private function addConstrantOnUser(UserInterface $user, string $entityString)
//    {
//        dd($user, $entityString);
//    }
}