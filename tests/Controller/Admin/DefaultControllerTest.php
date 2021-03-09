<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testIndexWithoutUser(): void
    {
        $this->client->request('GET', '/admin');

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testManageWithoutUser(): void
    {
        $this->client->request('GET', '/admin/manage');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testManageWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/manage');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testManageWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/manage');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testManageWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/manage');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}