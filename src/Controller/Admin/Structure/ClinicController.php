<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Clinic;
use App\Form\Structure\ClinicType;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/clinic", name="admin_clinic_")
 */
class ClinicController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ClinicController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/index", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('name', TextColumn::class, [
                'label'     => 'Nom de la structure',
                'orderable' => true,
//                'render'    => function ($value, $clinic) {
//                    return '<a href="/admin/clinic/show/' . $clinic->getId() . '">' . $value . '</a>';
//                }
            ])
            ->add('city', TextColumn::class, [
                'label'     => 'Ville de la structure',
                'orderable' => true,
//                'render'    => function ($value, $clinic) {
//                    return '<a href="/admin/clinic/show/' . $clinic->getId() . '">' . $value . '</a>';
//                }
            ])
            ->add('type', TextColumn::class, [
                'label'     => 'Type de structure',
                'orderable' => true,
//                'render'    => function ($value, $clinic) {
//                    return '<a href="/admin/clinic/show/' . $clinic->getId() . '">' . $value . '</a>';
//                }
            ])
            ->addOrderBy('name')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Clinic::class
            ])
        ;

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
}