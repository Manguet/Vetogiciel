<?php

namespace App\Command\Animal;

use App\Entity\Patients\Race;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to import base races in project
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportRacesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private const RACES_ARRAY = [
        'Chien',
        'Chat',
        'Oiseau',
        'Reptile',
        'Rongeur',
        'Vache',
        'Chevre',
        'Mouton',
        'Cochon',
        'Cheval',
    ];

    /**
     * ImportSpeciesCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string|null $name
     */
    public function __construct(EntityManagerInterface $entityManager, string $name = null)
    {
        $this->entityManager = $entityManager;

        parent::__construct($name);
    }

    /**
     * Configuration without argument
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('import:races')
            ->setDescription('Command to import basic races in project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->printStartText($io);

        $this->insertRacesInBdd($io);

        $this->printEndText($io);

        return 1;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return void
     * @throws Exception
     */
    private function printStartText(SymfonyStyle $io): void
    {
        $io->title('IMPORT RACES IN BDD');

        $io->createProgressBar(count(self::RACES_ARRAY));
        $io->progressStart();

        $io->newLine();
        $io->text('... Import start at ' . $this->getCurrentDate() . ' ...');
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    private function getCurrentDate(): string
    {
        $timeZone = new DateTimeZone('Europe/Paris');

        $date = new DateTime('now', $timeZone);

        return $date->format('d/m/Y H:i:s');
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return void
     */
    private function insertRacesInBdd(SymfonyStyle $io): void
    {
        foreach (self::RACES_ARRAY as $raceName) {

            if (!$this->alreadyExist($raceName)) {
                $race = new Race();

                $race->setName($raceName);

                $this->entityManager->persist($race);

                $io->progressAdvance();
            }

        }

        $io->progressFinish();

        $this->entityManager->flush();
    }

    /**
     * @param string $raceName
     *
     * @return bool
     */
    private function alreadyExist(string $raceName): bool
    {
       $exist = false;

       $race = $this->entityManager->getRepository(Race::class)
           ->findOneBy(['name' => $raceName]);

       if ($race) {
           $exist = true;
       }

       return $exist;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @throws Exception
     *
     * @return void
     */
    private function printEndText(SymfonyStyle $io): void
    {
        $io->text('... Import races end at : ' . $this->getCurrentDate() . ' ...');
    }
}