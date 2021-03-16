<?php

namespace App\Tests\Controller\Admin\Structure;

use App\Entity\Structure\Veterinary;
use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.maguet@gmail.com>
 */
class VeterinaryControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testIndexWithoutUser(): void
    {
        $this->client->request('GET', '/admin/veterinary');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/veterinary');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/veterinary');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/veterinary');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithoutUser(): void
    {
        $this->client->request('GET', '/admin/veterinary/new');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/veterinary/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/veterinary/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/veterinary/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithoutUser(): void
    {
        $this->client->request('GET', '/admin/veterinary/edit/');

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $veterinary = $entityManager->getRepository(Veterinary::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/veterinary/edit/' . $veterinary->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithEmployee(): void
    {
        $this->logIn('employee');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $veterinary = $entityManager->getRepository(Veterinary::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/veterinary/edit/' . $veterinary->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithClient(): void
    {
        $this->logIn('client');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $veterinary = $entityManager->getRepository(Veterinary::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/veterinary/edit/' . $veterinary->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}