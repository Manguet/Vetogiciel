<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Clinic;
use App\Form\Structure\ClinicType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
use App\Service\Priority\PriorityServices;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/clinic", name="admin_clinic_")
 *
 * @Security("is_granted('ADMIN_CLINIC_ACCESS')")
 */
class ClinicController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PriorityServices
     */
    private $priorityServices;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * ClinicController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PriorityServices $priorityServices
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $entityManager, PriorityServices $priorityServices,
                                SluggerInterface $slugger)
    {
        $this->entityManager    = $entityManager;
        $this->priorityServices = $priorityServices;
        $this->slugger          = $slugger;
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
        $table = $dataTableFactory->create()
            ->add('name', TextColumn::class, [
                'label'     => 'Nom de la structure',
                'orderable' => true,
                'render'    => function ($value, $clinic) {
                    return '<a href="/admin/clinic/edit/' . $clinic->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('city', TextColumn::class, [
                'label'     => 'Ville de la structure',
                'orderable' => true,
            ])
            ->add('type', TextColumn::class, [
                'label'     => 'Type de structure',
                'orderable' => true,
            ])
            ->add('priority', TextColumn::class, [
                'label'     => 'Priorité d\'affichage',
                'orderable' => true,
            ]);

        $datatableField
            ->addCreatedBy($table);

        $table
            ->addOrderBy('priority')
        ;

        $datatableField->createDatatableAdapter($table, Clinic::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/clinic/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newClinic(Request $request): Response
    {
        $clinic = new Clinic();

        $form = $this->createForm(ClinicType::class, $clinic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $priority = $this->priorityServices->setPriorityOnCreation($clinic);
            $clinic
                ->setPriority($priority)
                ->setNameSlugiffied($this->slugger->generateSlugUrl(
                    $clinic->getName(),
                    Clinic::class
                ))
            ;

            $this->entityManager->persist($clinic);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_clinic_index');
        }

        return $this->render('admin/clinic/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Clinic $clinic
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Clinic $clinic, Request $request): Response
    {
        $form = $this->createForm(ClinicType::class, $clinic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_clinic_index');
        }

        return $this->render('admin/clinic/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/priority", name="priority")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function priority(Request $request): Response
    {
        if (($request->getMethod() === 'POST') && ($request->isXmlHttpRequest())) {
            $movedClinics = $request->get('movedClinics');
            $newClinics   = $request->get('newClinics');
            $clinics      = $clinicPositions = [];

            $i = 0;
            if (is_array($movedClinics) && is_array($newClinics)) {

                foreach ($movedClinics as $movedClinic) {
                    $clinics[$movedClinic] = $newClinics[$i];
                    $i++;
                }

                foreach ($clinics as $oldPosition => $newPosition) {

                    $clinic = $this->entityManager->getRepository(Clinic::class)
                        ->findOneBy([
                            'priority' => (int)$oldPosition,
                        ])
                    ;

                    if ($clinic) {
                        $clinicPositions[$clinic->getId()] = $newPosition;
                    }
                }

                foreach ($clinicPositions as $id => $newPosition) {

                    $clinic = $this->entityManager->getRepository(Clinic::class)
                        ->find((int)$id)
                    ;

                    $clinic->setPriority($newPosition);
                    $this->entityManager->persist($clinic);
                }

                $this->entityManager->flush();

                return new JsonResponse('Priorité mise à jour', 200);
            }
            return new JsonResponse('Pas de modification', 200);

        }

        return new Response('error', 500);
    }
}