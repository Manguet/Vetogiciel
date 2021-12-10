<?php

namespace App\Command\Animal;

use App\Service\Command\ImportCommandServices;
use Exception;
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
 * Command to import dogs species
 * Step 1 : Use API to get dog species
 * Step 2 : create CSV with All dog species
 * Step 3 : import dogs in BDD
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportDogsCommand extends Command
{
    private ImportCommandServices $commandServices;

    /**
     * API Address
     */
    private const API_PATH = 'https://api.thedogapi.com/v1/breeds';

    /**
     * Path to folder to import CSV
     */
    private const PATH_TO_FOLDER = '/documents/import/animal/dogs.csv';

    /**
     * ImportDogsCommand constructor.
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
            ->setName('import:dogs')
            ->setDescription('Command to import dog species in DB. CSV file dogs.csv in document/import/animal')
            ->addArgument('step3', InputArgument::OPTIONAL, 'You can load dogs if you already have CSV with step3 argument')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Exception
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->commandServices->printStartText($io, 'IMPORT DOGS');

        $argument = $input->getArgument('step3');
        if ($argument === 'step3') {

            $this->commandServices->importAnimalsWithCSV($io, self::PATH_TO_FOLDER, 'Chien');

        } else {

            /** STEP 1 : API */
            $dogsData = $this->commandServices->getDatasFromApi($io, self::API_PATH);

            $dogs = $this->commandServices->improveAPIData($dogsData);

            /** STEP 2 : CSV */
            $this->commandServices->createCsv($dogs, $io, self::PATH_TO_FOLDER);

            /** STEP 3 : IMPORT */
            $this->commandServices->importAnimalsInDB($dogs, $io, 'Chien');

        }

        return 1;
    }
}