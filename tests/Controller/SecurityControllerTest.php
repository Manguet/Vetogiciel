<?php

namespace App\Tests\Controller;

use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SecurityControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testLoginWithoutUser(): void
    {
        $this->client->request('GET', '/security/login');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/security/login');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/security/login');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/security/login');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLogoutWithoutUser(): void
    {
        $this->client->request('GET', '/security/logout');

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLogoutWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/security/logout');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLogoutWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/security/logout');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLogoutWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/security/logout');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }
}