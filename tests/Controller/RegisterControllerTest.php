<?php

namespace App\Tests\Controller;

use App\Entity\Structure\Veterinary;
use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RegisterControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testRegistrationWithoutUser(): void
    {
        $this->client->request('GET', '/security/register');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRegistrationWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/security/register');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRegistrationWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/security/register');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRegistrationWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/security/register');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
    }
}