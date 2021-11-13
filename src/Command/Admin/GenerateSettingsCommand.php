<?php

namespace App\Command\Admin;

use App\DBAL\Types\Configuration\ConfigurationTypeEnum;
use App\Entity\Settings\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class GenerateSettingsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

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
            ->setName('generate:settings')
            ->setDescription('Command to import settings in project')
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

        $io->title('Settings generation started');

        $mapping = $this->getSettingsMapping();

        $this->generateGeneralSettings($io, $mapping);

        $io->success('Fin des imports de settings');

        return 0;
    }

    /**
     * @return mixed
     */
    private function getSettingsMapping()
    {
        $mappingFile = $this->kernel->getProjectDir() . '/documents/mapping/admin/import_settings.yaml';

        if (!file_exists($mappingFile)) {
            throw new FileNotFoundException('Le fichier ' . $mappingFile . ' n\'est pas présent');
        }

        return Yaml::parseFile($mappingFile);
    }

    /**
     * @param SymfonyStyle $io
     * @param $mapping
     *
     * @return void
     */
    private function generateGeneralSettings(SymfonyStyle $io, $mapping): void
    {
        $io->text('Début de l\'import des Generals Settings');
        $io->newLine();

        if (!isset($mapping['generals'])) {

            $io->note('Le fichier de mapping ne comprend pas generals');

            return;
        }

        foreach ($mapping['generals'] as $settingTitle => $settingDatas) {

            $isSettingExist = $this->entityManager->getRepository(Configuration::class)
                ->findOneBy(['name' => $settingTitle]);

            if (!$isSettingExist) {

                $configuration = new Configuration();

                $configuration->setName($settingTitle);

                foreach ($settingDatas as $title => $settingData) {

                    $configuration->{'set' . ucfirst($title)}($settingData);
                }

                $configuration->setConfigurationType(ConfigurationTypeEnum::GENERAL);

                $this->entityManager->persist($configuration);
            }
        }

        $io->note('Import avec succès des configurations générales');

        $this->entityManager->flush();
    }
}