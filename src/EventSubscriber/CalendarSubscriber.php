<?php

namespace App\EventSubscriber;

use App\Repository\Calendar\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Event to add bookings on calendar dynamically
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CalendarSubscriber implements EventSubscriberInterface
{
    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param BookingRepository $bookingRepository
     * @param UrlGeneratorInterface $router
     */
    public function __construct( BookingRepository $bookingRepository, UrlGeneratorInterface $router ) {
        $this->bookingRepository = $bookingRepository;
        $this->router            = $router;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @param CalendarEvent $calendar
     *
     * @return void
     */
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $start   = $calendar->getStart();
        $end     = $calendar->getEnd();
        $filters = $calendar->getFilters();

        /** Modify the query to fit to your entity and needs
         * Change booking.beginAt by your start date property
         */
        $bookings = $this->bookingRepository
            ->createQueryBuilder('booking')
            ->where('booking.beginAt BETWEEN :start and :end OR booking.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($bookings as $booking) {
            /** this create the events with data to fill calendar */
            $bookingEvent = new Event(
                $booking->getTitle(),
                $booking->getBeginAt(),
                $booking->getEndAt() /** If the end date is null or not defined, a all day event is created. */
            );

            /**
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $bookingEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor'     => 'red',
            ]);
            $bookingEvent->addOption(
                'url',
                $this->router->generate('booking_show', [
                    'id' => $booking->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}