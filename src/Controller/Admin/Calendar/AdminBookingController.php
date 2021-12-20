<?php

namespace App\Controller\Admin\Calendar;

use App\Entity\Calendar\Booking;
use App\Entity\Settings\Configuration;
use App\Entity\Settings\Role;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use App\Form\Animal\AnimalType;
use App\Form\Calendar\AdminBookingType;
use App\Form\Client\ClientFormType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Service\Calendar\AdminCalendarServices;
use App\Service\Calendar\AdminCalendarSettingsServices;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use LogicException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/calendar/booking", name="admin_calendar_booking_")
 * @Security("is_granted('ADMIN_CALENDAR_ACCESS')")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminBookingController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param DatatableFieldInterface $datatableField
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,
                          DatatableFieldInterface $datatableField): Response
    {
        $veterinary = $this->getVeterinary($request);

        $veterinaries = $this->entityManager->getRepository(Veterinary::class)
            ->findBy([
                'clinic'     => $this->getUser()->getClinic(),
                'isVerified' => true,
            ]);

        $role = $this->entityManager->getRepository(Role::class)
            ->findOneBy(['name' => $this->getUser()->getRoles()[0]]);

        $table = $dataTableFactory->create();

        $datatableField
            ->addFieldWithEditField($table, 'title',
                'Motif du rendez-vous',
                'calendar/booking',
                'ADMIN_BOOKING_SHOW',
                true
            )
            ->add('beginAt', DateTimeColumn::class, [
                'label'     => 'De ...',
                'orderable' => true,
                'format'    => 'd/m/Y H:i:s',
            ])
            ->add('endAt', DateTimeColumn::class, [
                'label'     => 'A ...',
                'orderable' => true,
                'format'    => 'd/m/Y H:i:s',
            ]);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/calendar/booking/include/_delete-button.html.twig', [
                'entity'         => 'booking',
                'authorizations' => 'ADMIN_BOOKING_DELETE'
            ])
            ->addOrderBy('beginAt')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Booking::class,
                'query'  => function (QueryBuilder $builder) use ($veterinary) {

                    $qb = $builder
                        ->select('b')
                        ->from(Booking::class, 'b')
                        ->andWhere('b.veterinary = :veterinary')
                        ->setParameter('veterinary', $veterinary->getId())
                    ;

                    $excludedStrings = $this->entityManager->getRepository(Configuration::class)
                        ->findOneBy(['name' => 'calendar_exeptions_' . $veterinary->getClinic()->getId()]);

                    if ($excludedStrings && isset($excludedStrings->getDatas()['values'])) {
                        $excludedStrings = str_replace(' ', '', $excludedStrings->getDatas()['values']);
                        $excludedStrings = explode(',', $excludedStrings);
                    }

                    if ($excludedStrings) {
                        $qb
                            ->andWhere('b.title NOT IN (:exceptions)')
                            ->setParameter('exceptions', $excludedStrings)
                        ;
                    }
                }
            ]);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/calendar/booking/index.html.twig', [
            'table'        => $table,
            'veterinary'   => $veterinary,
            'veterinaries' => $veterinaries,
            'role'         => $role
        ]);
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    private function getVeterinary(Request $request): object
    {
        $vetForAjax = $request->headers->get('referer');

        if ($vetForAjax) {
            $vetForAjax = explode('veterinary=', $vetForAjax);

            $vetForAjax = $vetForAjax[1] ?? null;
        }

        if (null === $vetForAjax
            && null === $request->query->get('veterinary')
            && $this->getUser() instanceof Veterinary)
        {
            return $this->getUser();
        }

        if (null === $vetForAjax
            && null === $request->query->get('veterinary')
            && $this->getUser() instanceof Employee)
        {
            return $this->entityManager->getRepository(Veterinary::class)
                ->findOneBy(['clinic' => $this->getUser()->getClinic()]);
        }

        if ($request->query->get('veterinary')) {
            return $this->entityManager->getRepository(Veterinary::class)
                ->find($request->query->get('veterinary'));
        }

        if ($vetForAjax) {
            return $this->entityManager->getRepository(Veterinary::class)
                ->find($vetForAjax);
        }

        throw new LogicException('Aucun vétérinaire trouvé', 400);
    }

    /**
     * @Route("/new/{id}", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CALENDAR_ADD')")
     *
     * @throws Exception
     */
    public function new(Request $request, Veterinary $veterinary, AdminCalendarSettingsServices $calendarServices): Response
    {
        $booking = new Booking();

        $form = $this->createForm(AdminBookingType::class, $booking, [
            'booking'    => $booking,
            'veterinary' => $veterinary
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formDatas = $request->get('admin_booking');

            $booking
                ->setBeginAt(DateTime::createFromFormat('d/m/Y H:i', $formDatas['beginAt']))
                ->setEndAt(DateTime::createFromFormat('d/m/Y H:i', $formDatas['endAt']))
                ->setVeterinary($veterinary)
            ;

            $client = $booking->getClient();

            if ($client && null === $client->getId()) {

                $client
                    ->setIsVerified(false)
                    ->setPassword('original=' . bin2hex(random_bytes(7)))
                    ->setEmail(bin2hex(random_bytes(7)) . '@vetogiciel.fr')
                    ->addClinic($veterinary->getClinic())
                ;

                $this->entityManager->persist($client);
            }

            $animal = $booking->getAnimal();

            if ($animal && null === $animal->getId()) {

                $animal->setClient($client);
                $this->entityManager->persist($animal);
            }

            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_calendar_booking_show', [
                'id' => $booking->getId()
            ]);
        }

        return $this->render('admin/calendar/booking/new.html.twig', [
            'veterinary' => $veterinary,
            'form'       => $form->createView(),
            'settings'   => $calendarServices->getCalendarSettings($veterinary)
        ]);
    }

    /**
     * @Route("/submit", name="submit")
     * @Security("is_granted('ADMIN_CALENDAR_ADD')")
     *
     * @param Request $request
     * @param AdminCalendarServices $calendarServices
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function submitRecurrentEvent(Request $request, AdminCalendarServices $calendarServices): JsonResponse
    {
        $motif        = $request->request->get('motif');
        $duration     = $request->request->get('duration');
        $length       = $request->request->get('lenght');
        $veterinaryId = $request->request->get('veterinary');

        $veterinary = $this->entityManager->getRepository(Veterinary::class)
            ->find($veterinaryId);

        $now     = new DateTime('now');
        $hour    = ((int)$now->format('H'));
        $minutes = ((int)$now->format('i'));

        $startAt = 0;
        if ($minutes <= 30) {
            $startAt = 30;
        }

        if (empty($motif) || empty($duration) || empty($length) || empty($veterinary)) {
            return new JsonResponse(
                'Une erreur est survenue',
                400
            );
        }

        switch ($length) {
            case 'today':
                $calendarServices->addTodayEvents($now, $hour, $duration, $startAt, $motif, $veterinary);
                break;

            case 'week':
                $calendarServices->addWeekEvents($now, $hour, $duration, $startAt, $motif, $veterinary);
                break;

            case 'month':
                $calendarServices->addMonthEvents($now, $hour, $duration, $startAt, $motif, $veterinary);
                break;

            default:
                return new JsonResponse('error', 400);
        }

        return new JsonResponse('success', 200);
    }

    /**
     * @Route("/show/{id}", name="show", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CALENDAR_SHOW', booking)")
     *
     * @param Request $request
     * @param Booking $booking
     *
     * @return Response
     */
    public function show(Request $request, Booking $booking): Response
    {
        $client = $booking->getClient();

        $form = $animalForm = null;
        if ($client) {
            $form = $this->createForm(ClientFormType::class, $client);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $this->entityManager->flush();

                return $this->redirectToRoute('admin_calendar_booking_show', [
                    'id' => $booking->getId()
                ]);
            }
        }

        if ($booking->getAnimal()) {

            $animalForm = $this->createForm(AnimalType::class, $booking->getAnimal(), [
                'isNew' => false,
            ]);

            $animalForm->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->entityManager->flush();

                return $this->redirectToRoute('admin_calendar_booking_show', [
                    'id' => $booking->getId()
                ]);
            }
        }

        return $this->render('admin/calendar/booking/show.html.twig', [
            'booking'    => $booking,
            'veterinary' => $booking->getVeterinary(),
            'clientForm' => $form ? $form->createView() : null,
            'animalForm' => $animalForm ? $animalForm->createView() : null
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CALENDAR_EDIT', booking)")
     *
     * @param Request $request
     * @param Booking $booking
     *
     * @return Response
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking, [
            'booking'    => $booking,
            'veterinary' => $booking->getVeterinary()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formDatas = $request->get('admin_booking');

            $booking
                ->setBeginAt(DateTime::createFromFormat('d/m/Y H:i', $formDatas['beginAt']))
                ->setEndAt(DateTime::createFromFormat('d/m/Y H:i', $formDatas['endAt']))
            ;

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_calendar_booking_index', [
                'id' => $booking->getVeterinary()->getId()
            ]);
        }

        return $this->render('admin/calendar/booking/edit.html.twig', [
            'booking'    => $booking,
            'form'       => $form->createView(),
            'veterinary' => $booking->getVeterinary()
        ]);
    }

    /**
     * @Route("/drag-and-drop/{id}", name="drag_and_drop", methods={"POST"})
     * @Security("is_granted('ADMIN_CALENDAR_EDIT', booking)")
     *
     * @param Request $request
     * @param Booking $booking
     *
     * @return JsonResponse
     */
    public function dragAndDrop(Request $request, Booking $booking): JsonResponse
    {
        $beginAt = DateTime::createFromFormat('d/m/Y H:i', $request->get('beginAt'));
        $endAt   = DateTime::createFromFormat('d/m/Y H:i', $request->get('endAt'));

        if (!$beginAt instanceof DateTimeInterface && !$endAt instanceof DateTimeInterface) {
            return new JsonResponse([
                'type'    => 'error',
                'message' => 'Une erreur est survenu lors de la mise à jour'
            ], 400);
        }

        if ($beginAt instanceof DateTimeInterface) {
            $booking->setBeginAt($beginAt);
        }

        if ($endAt instanceof DateTimeInterface) {
            $booking->setEndAt($endAt);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'type'    => 'success',
            'message' => 'Le rendez-vous a bien été mis à jour'
        ], 200);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @Security("is_granted('ADMIN_CALENDAR_DELETE', booking)")
     *
     * @param Request $request
     * @param Booking $booking
     *
     * @return Response
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if (!$booking instanceof Booking) {
            return new JsonResponse([
                'type'    => 'error',
                'message' => 'Une erreur est survenue'
            ], 404);
        }


        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        if ($request->getMethod() === 'POST') {
            return new JsonResponse([
                'type'    => 'success',
                'message' => 'Le rendez-vous a été supprimé avec succès'
            ], 200);
        }

        $this->addFlash('success', 'Le rendez-vous a été supprimé avec succès');
        return $this->redirectToRoute('admin_calendar_booking_index');
    }
}
