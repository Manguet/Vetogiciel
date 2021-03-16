<?php

namespace App\Tests\Controller\Admin\Ajax;

use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminAjaxControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testRaceWithoutUser(): void
    {
        $this->client->request('GET', '/admin/ajax/race');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRaceWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/ajax/race');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRaceWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/ajax/race');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRaceWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/ajax/race');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSpeciesWithoutUser(): void
    {
        $this->client->request('GET', '/admin/ajax/species');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSpeciesWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/ajax/species');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSpeciesWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/ajax/species');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSpeciesWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/ajax/species');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStructureWithoutUser(): void
    {
        $this->client->request('GET', '/admin/ajax/structure');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStructureWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/ajax/structure');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStructureWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/ajax/structure');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStructureWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/ajax/structure');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSectorWithoutUser(): void
    {
        $this->client->request('GET', '/admin/ajax/sector');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSectorWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/ajax/sector');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSectorWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/ajax/sector');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSectorWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/ajax/sector');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}