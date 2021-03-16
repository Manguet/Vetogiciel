<?php

namespace App\Tests\Controller\Calendar;

use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CalendarControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testIndexWithoutUser(): void
    {
        $this->client->request('GET', '/admin/calendar/index');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/admin/calendar/index');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/admin/calendar/index');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/admin/calendar/index');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}