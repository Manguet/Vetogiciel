<?php

namespace App\EventSubscriber\Calendar;

use App\Repository\Calendar\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CalendarSubscriber implements EventSubscriberInterface
{
    private BookingRepository $bookingRepository;
    private UrlGeneratorInterface $router;

    public function __construct(
        BookingRepository $bookingRepository,
        UrlGeneratorInterface $router
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @param CalendarEvent $calendar
     */
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $start   = $calendar->getStart();
        $end     = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $bookings = $this->bookingRepository
            ->createQueryBuilder('booking')
            ->where('booking.beginAt BETWEEN :start and :end OR booking.endAt BETWEEN :start and :end')
            ->andWhere('booking.veterinary = :veterinary')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->setParameter('veterinary', $filters['veterinary'])
            ->getQuery()
            ->getResult()
        ;

        foreach ($bookings as $booking) {

            $bookingEvent = new Event(
                $booking->getTitle(),
                $booking->getBeginAt(),
                $booking->getEndAt(),
            );

            $bookingEvent->setOptions([
                'backgroundColor'   => $booking->getColor() ?? '#B2BABB',
                'borderColor'       => $booking->getColor() ?? '#B2BABB',
                'id'                => $booking->getId(),
            ]);
            $bookingEvent->addOption(
                'url',
                $this->router->generate('admin_calendar_booking_show', [
                    'id' => $booking->getId(),
                ])
            );

            $calendar->addEvent($bookingEvent);
        }
    }
}