<?php

namespace App\Command\Authorization;

use App\Entity\Settings\Authorization;
use App\Entity\Settings\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportRolesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private KernelInterface $kernel;

    /**
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     * @param string|null $name
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel,
                                string $name = null)
    {
        $this->entityManager = $entityManager;
        $this->kernel        = $kernel;

        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('import:roles')
            ->setDescription('Command to import roles in database')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Debut de l\'import des rôles');

        $yaml = Yaml::parseFile($this->kernel->getProjectDir() . '/documents/mapping/admin/import_roles.yaml');

        $this->importRoles($yaml, $io);

        $this->entityManager->flush();

        $io->success('Fin de l\'import des rôles');
        return 0;
    }

    /**
     * @param array $yaml
     * @param SymfonyStyle $io
     *
     * @return void
     */
    private function importRoles(array $yaml, SymfonyStyle $io): void
    {
        foreach ($yaml as $roleName => $settings) {

            $roleInBdd = $this->entityManager->getRepository(Role::class)
                ->findOneBy(['name' => $roleName]);

            if (!$roleInBdd) {

                $this->createNewRole($io, $roleName, $settings);
            }
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param string $roleName
     * @param array $settings
     *
     * @return void
     */
    private function createNewRole(SymfonyStyle $io, string $roleName, array $settings): void
    {
        $role = new Role();

        $role
            ->setName($roleName)
            ->setType('system')
        ;

        $this->addAuthorizationsInRole($settings, $role, $io);

        $this->entityManager->persist($role);
    }

    /**
     * @param array $settings
     * @param Role $role
     * @param SymfonyStyle $io
     *
     * @return void
     */
    private function addAuthorizationsInRole(array $settings, Role $role, SymfonyStyle $io): void
    {
        foreach ($settings as $settingTitle => $settingDatas) {

            if ($settingTitle === 'level') {

                $role->setPermissionLevel($settingDatas);

            } elseif ($settingTitle === 'entities') {

                $this->addEntitiesAuthorizations($settingDatas, $role);

            } else {
                $io->error('Le champs ' . $settingTitle . 'n\'est pas pris en comptes');
                exit();
            }
        }
    }

    /**
     * @param array $settingDatas
     * @param Role $role
     *
     * @return void
     */
    private function addEntitiesAuthorizations(array $settingDatas, Role $role): void
    {
        foreach ($settingDatas as $sectorTitle => $sectorData) {

            if ($sectorData === 'all') {

                $role->addAuthorization($sectorTitle . '_FULL_ACCESS');

            } else {

                $this->addAuthorizationsByYamlLogic($sectorData, $role, $sectorTitle);
            }
        }
    }

    /**
     * @param array $sectorDatas
     * @param Role $role
     * @param string $sectorTitle
     *
     * @return void
     */
    private function addAuthorizationsByYamlLogic(array $sectorDatas, Role $role, string $sectorTitle): void
    {
        foreach ($sectorDatas as $entityTitle => $data) {

            $authorization = $this->entityManager->getRepository(Authorization::class)
                ->findOneBy(['relatedEntity' => $entityTitle]);

            if (!$authorization) {
                return;
            }

            if ($data === 'all') {

                $role->addAuthorization($sectorTitle . '_' . strtoupper($entityTitle) . '_MANAGE');

            } else {

                $this->addAuthorizationsMinorLevel($authorization, $role, $data, $sectorTitle);
            }
        }
    }

    /**
     * @param Authorization $authorization
     * @param Role $role
     * @param array $data
     * @param string $sectorTitle
     *
     * @return void
     */
    private function addAuthorizationsMinorLevel(Authorization $authorization, Role $role, array $data,
                                                 string $sectorTitle): void
    {
        if (isset($data['access']) && $data['access'] === 'yes' && $authorization->getCanAccess()) {

            $role->addAuthorization($sectorTitle . '_' . $authorization->getCanAccess());
        }
        if (isset($data['add']) && $data['add'] === 'yes' && $authorization->getCanAdd()) {

            $role->addAuthorization($sectorTitle . '_' . $authorization->getCanAdd());
        }
        if (isset($data['show']) && $data['show'] === 'yes' && $authorization->getCanShow()) {

            $role->addAuthorization($sectorTitle . '_' . $authorization->getCanShow());
        }
        if (isset($data['edit']) && $data['edit'] === 'yes' && $authorization->getCanEdit()) {

            $role->addAuthorization($sectorTitle . '_' . $authorization->getCanEdit());
        }
        if (isset($data['delete']) && $data['delete'] === 'yes' && $authorization->getCanDelete()) {

            $role->addAuthorization($sectorTitle . '_' . $authorization->getCanDelete());
        }
    }
}