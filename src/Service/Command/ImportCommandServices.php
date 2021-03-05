<?php

namespace App\Service\Command;

use App\Entity\Patients\Race;
use App\Entity\Patients\Species;
use App\Service\Dates\DateServices;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Csv\CannotInsertRecord;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportCommandServices
{
    /**
     * @var DateServices
     */
    private $dateServices;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param DateServices $dateServices
     * @param HttpClientInterface $client
     * @param KernelInterface $kernel
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(DateServices $dateServices, HttpClientInterface $client,
                                KernelInterface $kernel, EntityManagerInterface $entityManager)
    {
        $this->dateServices  = $dateServices;
        $this->client        = $client;
        $this->kernel        = $kernel;
        $this->entityManager = $entityManager;
    }

    /**
     * @param SymfonyStyle $io
     * @param string $title
     *
     * @return void
     * @throws Exception
     */
    public function printStartText(SymfonyStyle $io, string $title): void
    {
        $io->title($title);

        $io->newLine();

        $io->text('... Import Start at ' . $this->dateServices->getCurrentDate() . ' ...');
    }

    /**
     * @param SymfonyStyle $io
     * @param string $url
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array
     */
    public function getDatasFromApi(SymfonyStyle $io, string $url): array
    {
        $io->newLine();
        $io->text('STEP 1 : Create array from API');

        $response = $this->client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new RuntimeException('URL error, please contact an administrator');
        }

        $io->newLine();
        $io->text('... API requested with success ...');

        return $response->toArray();
    }

    /**
     * @param array $animals
     *
     * @return array
     */
    public function improveAPIData(array $animals): array
    {
        $animalsName = [];

        foreach ($animals as $animal) {
            $animalsName[] = ['name' => $animal['name']];
        }

        return $animalsName;
    }

    /**
     * @param array $animals
     * @param SymfonyStyle $io
     * @param $pathToFolder
     *
     * @throws CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    public function createCsv(array $animals, SymfonyStyle $io, $pathToFolder): void
    {
        $io->newLine(2);
        $io->text('STEP 2 : CREATE CSV FILE');

        $io->newLine();
        $io->text('... Create CSV file ...');

        $writer = Writer::createFromPath($this->kernel->getProjectDir() . $pathToFolder, 'w+');

        $writer->setDelimiter(';');

        $writer->insertOne(['name']);

        $writer->insertAll($animals);

        $io->text('... CSV created with success');
    }

    /**
     * @param array $animals
     * @param SymfonyStyle $io
     * @param string $animalName
     *
     * @throws Exception
     *
     * @return void
     */
    public function importAnimalsInDB(array $animals, SymfonyStyle $io, string $animalName): void
    {
        $io->newLine(2);
        $io->text('STEP 3 : IMPORT IN DATABASE');

        $io->createProgressBar(count($animals));
        $io->progressStart();

        $animalRace = $this->entityManager->getRepository(Race::class)
            ->findOneBy(['name' => $animalName]);

        if (!$animalRace) {
            throw new RuntimeException('Animal not find in DB, please launch php bin/console import:races first');
        }

        $this->importOneByOne($animals, $io, $animalRace);
    }

    /**
     * @param mixed $animals
     * @param SymfonyStyle $io
     * @param Race $animalRace
     *
     * @throws Exception
     *
     * @return void
     */
    private function importOneByOne($animals, SymfonyStyle $io, Race $animalRace): void
    {
        foreach ($animals as $animal) {

            if (!$this->alreadyExist($animal['name'])) {

                $species = new Species();

                $species->setName($animal['name']);
                $species->setRace($animalRace);

                $this->entityManager->persist($species);

                $io->progressAdvance();
            }
        }

        $this->entityManager->flush();

        $io->progressFinish();

        $io->newLine();
        $io->text('... Import in DB end at ' . $this->dateServices->getCurrentDate() . ' ...');
    }

    /**
     * @param string $animalName
     *
     * @return bool
     */
    private function alreadyExist(string $animalName): bool
    {
        $exist = true;

        $animalData = $this->entityManager->getRepository(Species::class)
            ->findOneBy(['name' => $animalName]);

        if (!$animalData) {
            $exist = false;
        }

        return $exist;
    }

    /**
     * @param SymfonyStyle $io
     * @param string $pathToFolder
     * @param string $animalName
     *
     * @return void
     * @throws Exception
     *
     * @throws \League\Csv\Exception
     */
    public function importAnimalsWithCSV(SymfonyStyle $io, string $pathToFolder, string $animalName): void
    {
        $io->newLine(2);
        $io->text('STEP 3 : IMPORT IN DATABASE WITH CSV');

        $csv = Reader::createFromPath($this->kernel->getProjectDir() . $pathToFolder, 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset(0)
            ->limit(count($csv))
        ;

        $io->createProgressBar(count($csv));
        $io->progressStart();

        $animalRace = $this->entityManager->getRepository(Race::class)
            ->findOneBy(['name' => $animalName]);

        if (!$animalRace) {
            throw new RuntimeException('Animal not find in DB, please launch php bin/console import:races first');
        }

        $records = $stmt->process($csv);

        $this->importOneByOne($records, $io, $animalRace);
    }
}