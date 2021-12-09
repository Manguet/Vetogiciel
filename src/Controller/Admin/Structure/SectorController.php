<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Sector;
use App\Form\Structure\SectorFormType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/sector", name="admin_sector_")
 */
class SectorController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
        $table = $dataTableFactory->create()
            ->add('name', TextColumn::class, [
                'label'     => 'Nom du secteur',
                'orderable' => true,
                'render'    => function ($value, $sector) {

                    return '<a href="/admin/sector/edit/' . $sector->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('veterinaries', TextColumn::class, [
                'label'     => 'Nombre de vétérinaires',
                'orderable' => true,
                'render'    => function ($value, $sector) {

                    if (null !== $sector->getVeterinaries()) {
                        return count($sector->getVeterinaries());
                    }

                    return 0;
                }
            ])
            ->add('employees', TextColumn::class, [
                'label'     => 'Nombre d\'employés',
                'orderable' => true,
                'render'    => function ($value, $sector) {

                    if (null !== $sector->getEmployees()) {
                        return count($sector->getEmployees());
                    }

                    return 0;
                }
            ]);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/sector/include/_delete-button.html.twig', [
                'entity' => 'sector'
            ])
            ->addOrderBy('name')
        ;

        $datatableField->createDatatableAdapter($table, Sector::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/sector/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function newSector(Request $request): Response
    {
        $sector = new Sector();

        $form = $this->createForm(SectorFormType::class, $sector);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($sector);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_sector_index');
        }

        return $this->render('admin/sector/new.html.twig', [
            'form'           => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Sector $sector
     *
     * @return Response
     */
    public function editSector(Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorFormType::class, $sector);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($sector);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_sector_index');
        }

        return $this->render('admin/sector/new.html.twig', [
            'form'           => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Sector $sector
     *
     * @return JsonResponse
     */
    public function delete(Sector $sector): JsonResponse
    {
        if (!$sector instanceof Sector) {
            return new JsonResponse('Sector Not Found', 404);
        }

        $this->entityManager->remove($sector);
        $this->entityManager->flush();

        return new JsonResponse('Sector deleted with success', 200);
    }
}