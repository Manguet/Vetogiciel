<?php

namespace App\Tests\Controller\Calendar;

use App\Entity\Calendar\Booking;
use App\Tests\Traits\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class BookingControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @return void
     */
    public function testIndexWithoutUser(): void
    {
        $this->client->request('GET', '/calendar/booking');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/calendar/booking');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/calendar/booking');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIndexWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/calendar/booking');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithoutUser(): void
    {
        $this->client->request('GET', '/calendar/booking/new');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $this->client->request('GET', '/calendar/booking/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithEmployee(): void
    {
        $this->logIn('employee');

        $this->client->request('GET', '/calendar/booking/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNewWithClient(): void
    {
        $this->logIn('client');

        $this->client->request('GET', '/calendar/booking/new');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testShowWithoutUser(): void
    {
        $this->client->request('GET', '/calendar/booking/show/');

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testShowWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/show/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testShowWithEmployee(): void
    {
        $this->logIn('employee');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/show/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testShowWithClient(): void
    {
        $this->logIn('client');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/show/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithoutUser(): void
    {
        $this->client->request('GET', '/calendar/booking/edit/');

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithVeterinary(): void
    {
        $this->logIn('veterinary');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/edit/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithEmployee(): void
    {
        $this->logIn('employee');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/edit/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditWithClient(): void
    {
        $this->logIn('client');

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $booking = $entityManager->getRepository(Booking::class)
            ->findOneBy([]);

        $this->client->request('GET', '/calendar/booking/edit/' . $booking->getId());

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}