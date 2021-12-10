<?php

namespace App\Security\Voters;

use App\Entity\Settings\Role;
use App\Traits\Voter\VoterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminEditVoter extends Voter
{
    use VoterTrait;

    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return (0 === strpos($attribute, "ADMIN_")
            && null !== $subject
            && (false !== strpos($attribute, "_EDIT")
                || false !== strpos($attribute, "_MANAGE")
                || $attribute === 'ADMIN_FULL_ACCESS'));    }

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

        if (!(in_array('ADMIN_FULL_ACCESS', $this->allRoles, true)
            || in_array('ADMIN_' . $entity . '_MANAGE', $this->allRoles, true)
            || in_array('ADMIN_' . $entity . '_EDIT', $this->allRoles, true)))
        {
            return false;
        }

        return $this->checkLevelAuthorization($role->getPermissionLevel(), $subject, $user);
    }
}