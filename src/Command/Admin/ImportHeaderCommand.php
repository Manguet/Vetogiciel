<?php

namespace App\Command\Admin;

use App\Entity\Admin\Header;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Manguet <benjamin.manguet>
 *
 * Command to import header in BDD.
 * Follow questions
 */
class ImportHeaderCommand extends Command
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
    public function __construct(EntityManagerInterface $entityManager,
                                KernelInterface $kernel,
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
            ->setName('import:header')
            ->setDescription('Command to import header for specific project')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws ConnectionException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Import du HEADER ADMIN');

        $question = $io->askQuestion(new ChoiceQuestion(
            'Voulez-vous vraiment effectuer cette action ?',
            ['oui', 'non'],
            'non'
        ));

        if ('non' === $question) {
            $io->success('Nous n\'avons pas importé le header');
            return 0;
        }

        $io->section('Type d\'import');

        $question = $io->askQuestion(new ChoiceQuestion(
            'Voulez-vous un import standard ou spécifique ?',
            ['standard', 'spécifique'],
            'standard'
        ));

        if ('standard' === $question) {
            $this->standardImport();

            $io->success('Import standard terminé');
            return 0;

        }

        $io->error('Import spécifique terminé non configuré actuellement');
        return 0;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    private function standardImport(): void
    {
        $yamlMappings = $this->truncateAndGetMapping();

        foreach ($yamlMappings as $title => $datas) {

            $header = new Header();

            $header
                ->setTitle($title)
                ->setIcon($datas['icon'] ?? 'fas fa-long-arrow-alt-right')
                ->setIsActivated(true)
                ->setIsMainHeader(!isset($datas['childs']))
            ;

            if (!$header->getIsMainHeader()) {

                foreach ($datas['childs'] as $titleChild => $childDatas) {

                    $childHeader = new Header();
                    $childHeader
                        ->setTitle($titleChild)
                        ->setIcon($childDatas['icon' ?? null])
                        ->setPath($childDatas['path' ?? '#'])
                        ->setIsActivated(true)
                        ->setParentHeader($header)
                        ->setIsMainHeader(true)
                    ;

                    $this->entityManager->persist($childHeader);
                }
            }

            $this->entityManager->persist($header);
        }

        $this->entityManager->flush();
    }

    /**
     * @return mixed
     *
     * @throws ConnectionException
     * @throws Exception
     */
    private function truncateAndGetMapping()
    {
        $isHeaderInBdd = $this->entityManager->getRepository(Header::class)
            ->findOneBy([]);

        if ($isHeaderInBdd) {
            $this->truncateHeaderTable();
        }

        $mapping = $this->kernel->getProjectDir() . '/documents/mapping/admin/import_header.yaml';

        if (!file_exists($mapping)) {
            throw new FileNotFoundException($mapping . ' n\'existe pas');
        }

        return Yaml::parseFile($mapping);
    }

    /**
     * @return void
     *
     * @throws ConnectionException
     * @throws Exception
     */
    private function truncateHeaderTable(): void
    {
        $classMetaData = $this->entityManager->getClassMetadata(Header::class);
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeStatement($q);
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (Exception $e) {
            $connection->rollback();
        }
    }
}