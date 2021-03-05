<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ImportDogsCommandTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testExecuteWithoutArguments(): void
    {
        $kernel      = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('import:dogs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        self::assertStringContainsString(1, $output);
    }

    /**
     * @return void
     */
    public function testExecuteWithArgument(): void
    {
        $kernel      = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('import:dogs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'step3'    => 'step3',
        ]);

        $output = $commandTester->getDisplay();

        self::assertStringContainsString(1, $output);
    }
}