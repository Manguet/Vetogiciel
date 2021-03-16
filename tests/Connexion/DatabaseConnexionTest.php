<?php

namespace App\Tests\Connexion;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DatabaseConnexionTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return void
     */
    public function testMySQLConnxion(): void
    {
        $this->entityManager->getConnection()->connect();

        self::assertEquals(true, $this->entityManager->getConnection()->isConnected());
    }
}