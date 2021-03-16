<?php

namespace App\Tests\Controller\Admin\Patient;

use App\Entity\Patients\Client;
use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AnimalControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testNewWithoutUser(): void
    {
        $this->client->request('GET', '/admin/animal/new/', [
            'id' => null,
        ]);

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/animal/new/' . $userClient->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithEmployee(): void
    {
        $this->logIn('employee');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/animal/new/' . $userClient->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithClient(): void
    {
        $this->logIn('client');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $this->client->request('GET', '/admin/animal/new/' . $userClient->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithoutUser(): void
    {
        $this->client->request('GET', '/admin/animal/edit/', [
            'id'     => null,
            'animal' => null,
        ]);

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $animal = $userClient->getAnimals()[0];

        $this->client->request('GET', '/admin/animal/edit/' . $userClient->getId() . '/animal/' . $animal->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithEmployee(): void
    {
        $this->logIn('employee');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $animal = $userClient->getAnimals()[0];

        $this->client->request('GET', '/admin/animal/edit/' . $userClient->getId() . '/animal/' . $animal->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithClient(): void
    {
        $this->logIn('client');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $userClient = $entityManager->getRepository(Client::class)
            ->findOneBy([]);

        $animal = $userClient->getAnimals()[0];

        $this->client->request('GET', '/admin/animal/edit/' . $userClient->getId() . '/animal/' . $animal->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}