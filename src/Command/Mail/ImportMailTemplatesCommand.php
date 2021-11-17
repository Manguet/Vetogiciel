<?php

namespace App\Command\Mail;

use App\Entity\Mail\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportMailTemplatesCommand extends Command
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
            ->setName('import:mail:templates')
            ->setDescription('Command to import mail templates in database')
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

        $io->title('Templates import started');

        $files = $this->getTemplates();

        $io->createProgressBar(count($files));
        $io->progressStart();
        foreach ($files as $file) {

            $this->importTemplate($file->getPathname(), $file->getFilename());
            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();
        $io->success('Fin des imports de template mail');

        return 0;
    }

    /**
     * @return Finder
     */
    private function getTemplates(): Finder
    {
        $finder = new Finder();

        $finder->files()->in($this->kernel->getProjectDir() . '/templates/email/');

        if (!$finder->hasResults()) {
            throw new FileNotFoundException('Aucun template dans le dossier templates/email');
        }

        return $finder;
    }

    /**
     * @param string $pathName
     * @param string $fileName
     */
    private function importTemplate(string $pathName, string $fileName): void
    {
        $email = new Email();

        $email
            ->setTitle($fileName)
            ->setTemplate(file_get_contents($pathName))
        ;

        $this->entityManager->persist($email);
    }
}