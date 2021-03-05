<?php

namespace App\Command\Animal;

use App\Service\Command\ImportCommandServices;
use Exception;
use League\Csv\CannotInsertRecord;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Command to import cats species
 * Step 1 : Use API to get cat species
 * Step 2 : create CSV with All cats species
 * Step 3 : import cats in BDD
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportCatsCommand extends Command
{
    /**
     * @var ImportCommandServices
     */
    private $commandServices;

    /**
     * API Address
     */
    private const API_PATH = 'https://api.thecatapi.com/v1/breeds';

    /**
     * Path to folder to import CSV
     */
    private const PATH_TO_FOLDER = '/documents/import/animal/cat.csv';

    /**
     * ImportCatsCommand constructor.
     *
     * @param ImportCommandServices $commandServices
     * @param string|null $name
     */
    public function __construct(ImportCommandServices $commandServices, string $name = null)
    {
        $this->commandServices = $commandServices;

        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('import:cats')
            ->setDescription('Command to import cat species in DB. CSV file cats.csv in document/import/animal')
            ->addArgument('step3', InputArgument::OPTIONAL, 'You can load cats if you already have CSV file')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws CannotInsertRecord
     * @throws \League\Csv\Exception
     * @throws Exception
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->commandServices->printStartText( $io,'IMPORT CATS');

        $argument = $input->getArgument('step3');
        if ($argument === 'step3') {

            $this->commandServices->importAnimalsWithCSV($io, self::PATH_TO_FOLDER, 'Chien');

        } else {

            /** STEP 1 : API */
            $catsData = $this->commandServices->getDatasFromApi($io, self::API_PATH);

            $cats = $this->commandServices->improveAPIData($catsData);

            /** STEP 2 : CSV */
            $this->commandServices->createCsv($cats, $io, self::PATH_TO_FOLDER);

            /** STEP 3 : IMPORT */
            $this->commandServices->importAnimalsInDB($cats, $io, 'Chat');
        }

        return 1;
    }
}