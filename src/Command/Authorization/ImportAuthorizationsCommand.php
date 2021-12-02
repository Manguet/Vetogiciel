<?php

namespace App\Command\Authorization;

use App\Entity\Settings\Authorization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportAuthorizationsCommand extends Command
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
            ->setName('import:authorizations')
            ->setDescription('Command to import authorizations in database')
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
        $io->title('Debut de l\'import des autorizations');

        $entities = $this->getEntities();

        $io->createProgressBar(count($entities));
        $io->progressStart();

        $this->importEntities($entities, $io);

        $io->progressFinish();
        $this->entityManager->flush();

        $io->success('Fin de l\'import des autorisations');
        return 0;
    }

    /**
     * @return Finder
     */
    private function getEntities(): Finder
    {
        $finder = new Finder();

        return $finder->in($this->kernel->getProjectDir() . '/src/Entity');
    }

    /**
     * @param Finder $entities
     * @param SymfonyStyle $io
     */
    private function importEntities(Finder $entities, SymfonyStyle $io): void
    {
        foreach ($entities as $entity) {

            $fileName = str_replace('.php', '', $entity->getFilename());

            $entityInBDD = $this->entityManager->getRepository(Authorization::class)
                ->findOneBy(['relatedEntity' => $fileName]);

            if (!$entityInBDD && $fileName !== 'MailMessage') {

                $authorization = new Authorization();

                $authorization
                    ->setRelatedEntity($fileName)
                    ->setCanAccess(strtoupper($fileName) . '_ACCESS')
                    ->setCanShow(strtoupper($fileName) . '_SHOW')
                    ->setCanAdd(strtoupper($fileName) . '_ADD')
                    ->setCanEdit(strtoupper($fileName) . '_EDIT')
                    ->setCanDelete(strtoupper($fileName) . '_DELETE')
                ;

                $this->entityManager->persist($authorization);
            }

            $io->progressAdvance();
        }
    }
}