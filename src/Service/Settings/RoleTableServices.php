<?php

namespace App\Service\Settings;

use App\Entity\Settings\Authorization;
use App\Entity\Settings\Role;
use App\Interfaces\Settings\RoleTableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RoleTableServices implements RoleTableInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private const FULL_ACCESS = '_FULL_ACCESS';

    /**
     * @var string
     */
    private const MANAGE = '_MANAGE';

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @param Role $role
     *
     * @return void
     */
    public function updateAuthorizationsInRole(Request $request, Role $role): void
    {
        $authorization = $request->request->get('authorization');
        $checked       = $request->request->get('checked');

        $explodedAuthorization = explode('_', $authorization);
        [$sector, $relatedEntity, $action] = $explodedAuthorization;

        /**
         * @var Role $role
         */
        if ($checked === 'true') {

            $this->addAuthorizations($authorization, $sector, $role);

        } else {

            $this->removeAuthorizations($sector, $role, $authorization, $relatedEntity, $action);

        }

        $this->entityManager->flush();
    }

    /**
     * @param string $authorization
     * @param string $sector
     * @param Role $role
     *
     * @return void
     */
    private function addAuthorizations(string $authorization, string $sector, Role $role): void
    {
        if ($authorization === $sector . self::FULL_ACCESS) {
            $this->removeAllAuthorizationsInSector($role, $sector);
        }

        $role->addAuthorization($authorization);
    }

    /**
     * @param string $sector
     * @param Role $role
     * @param string $authorization
     * @param string $relatedEntity
     * @param string $action
     *
     * @return void
     */
    private function removeAuthorizations(string $sector, Role $role,
                                          string $authorization, string $relatedEntity,
                                          string $action): void
    {
        if (in_array($sector . self::FULL_ACCESS, $role->getAuthorizations(), true)) {

            $this->updateAuthorizationsFromFullAccess($role, $sector, $authorization, $relatedEntity, $action);

        } elseif (in_array($sector . '_' . $relatedEntity . self::MANAGE, $role->getAuthorizations(), true)) {

            $this->updateAuthorizationsFromManage($relatedEntity, $role, $sector, $action);

        } else {

            $role->removeAuthorization($authorization);
        }
    }

    /**
     * @param Role $role
     * @param string $sector
     * @param string $authorization
     * @param string $relatedEntity
     * @param string $action
     *
     * @return void
     */
    private function updateAuthorizationsFromFullAccess(Role $role, string $sector, string $authorization,
                                                        string $relatedEntity, string $action): void
    {
        $this->removeAllAuthorizationsInSector($role, $sector);


        if ($authorization !== $sector . self::FULL_ACCESS) {

            $this->addAuthorizationsAfterFullAccessRemove($relatedEntity, $role, $sector, $authorization, $action);
        }
    }

    /**
     * @param string $relatedEntity
     * @param Role $role
     * @param string $sector
     * @param string $authorization
     * @param string $action
     *
     * @return void
     */
    private function addAuthorizationsAfterFullAccessRemove(string $relatedEntity, Role $role, string $sector,
                                                            string $authorization, string $action): void
    {
        $authorizations = $this->entityManager->getRepository(Authorization::class)
            ->findAll();

        foreach ($authorizations as $authorizationItem) {

            if ($relatedEntity !== strtoupper($authorizationItem->getRelatedEntity())) {

                $role->addAuthorization(
                    $sector . '_' . strtoupper($authorizationItem->getRelatedEntity()) . self::MANAGE
                );

            } elseif ($authorization !== $sector . '_' . strtoupper($authorizationItem->getRelatedEntity()) . self::MANAGE) {

                $role->removeAuthorization(
                    $sector . '_' . strtoupper($authorizationItem->getRelatedEntity()) . self::MANAGE
                );

                $this->addAllAuthorizationsAfterRemove($action, $authorizationItem, $role, $sector);

            }
        }
    }

    /**
     * @param string $action
     * @param Authorization $authorization
     * @param Role $role
     * @param string $sector
     *
     * @return void
     */
    private function addAllAuthorizationsAfterRemove(string $action, Authorization $authorization,
                                                     Role $role, string $sector): void
    {
        if ($action !== 'ACCESS' && $authorization->getCanAccess()) {
            $role->addAuthorization($sector . '_' . $authorization->getCanAccess());
        }

        if ($action !== 'ADD' && $authorization->getCanAdd()) {
            $role->addAuthorization($sector . '_' . $authorization->getCanAdd());
        }

        if ($action !== 'SHOW' && $authorization->getCanShow()) {
            $role->addAuthorization($sector . '_' . $authorization->getCanShow());
        }

        if ($action !== 'EDIT' && $authorization->getCanEdit()) {
            $role->addAuthorization($sector . '_' . $authorization->getCanEdit());
        }

        if ($action !== 'DELETE' && $authorization->getCanDelete()) {
            $role->addAuthorization($sector . '_' . $authorization->getCanDelete());
        }
    }

    /**
     * @param string $relatedEntity
     * @param Role $role
     * @param string $sector
     * @param string $action
     *
     * @return void
     */
    private function updateAuthorizationsFromManage(string $relatedEntity, Role $role,
                                                    string $sector, string $action): void
    {
        $authorizationInBdd = $this->entityManager->getRepository(Authorization::class)
            ->findOneBy(['relatedEntity' => ucfirst($relatedEntity)]);

        $role->removeAuthorization($sector . '_' . strtoupper($relatedEntity) . self::MANAGE);

        if ($action === 'ACCESS' && $authorizationInBdd->getCanAccess()) {
            $role->addAuthorization($sector . '_' . $authorizationInBdd->getCanAccess());
        }
        if ($action === 'ADD' && $authorizationInBdd->getCanAdd()) {
            $role->addAuthorization($sector . '_' . $authorizationInBdd->getCanAdd());
        }
        if ($action === 'SHOW' && $authorizationInBdd->getCanShow()) {
            $role->addAuthorization($sector . '_' . $authorizationInBdd->getCanShow());
        }
        if ($action === 'EDIT' && $authorizationInBdd->getCanEdit()) {
            $role->addAuthorization($sector . '_' . $authorizationInBdd->getCanEdit());
        }
        if ($action === 'DELETE' && $authorizationInBdd->getCanDelete()) {
            $role->addAuthorization($sector . '_' . $authorizationInBdd->getCanDelete());
        }
    }

    /**
     * @param Role $role
     * @param string $sector
     *
     * @return void
     */
    private function removeAllAuthorizationsInSector(Role $role, string $sector): void
    {
        foreach ($role->getAuthorizations() as $authorizationInRole) {

            if (0 === strpos($authorizationInRole, $sector)) {

                $role->removeAuthorization($authorizationInRole);
            }
        }
    }
}