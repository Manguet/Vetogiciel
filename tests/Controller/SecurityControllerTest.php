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

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/security/login');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/security/login');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/security/login');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }
}