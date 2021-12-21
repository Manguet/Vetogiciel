<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Prestation;
use App\Form\Structure\PrestationType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use Doctrine\ORM\EntityManagerInterface;
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
 * @Route("/admin/medecine", name="admin_medecine_")
 *
 * @Security("is_granted('ADMIN_PRESTATION_ACCESS')")
 */
class MedecineController extends AbstractController
{
    private EntityManagerInterface $entityManager;

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
        $table = $dataTableFactory->create();

        $datatableField
            ->addFieldWithEditField($table, 'title',
                'Nom du médicament',
                'medecine',
                'ADMIN_PRESTATION_EDIT'
            )
            ->add('code', TextColumn::class, [
                'label'     => 'Code medicament',
                'orderable' => true,
            ])
            ->add('PriceHT', TextColumn::class, [
                'label'     => 'Prix HT',
                'orderable' => true,
            ])
            ->add('PriceTTC', TextColumn::class, [
                'label'     => 'Prix TTC',
                'orderable' => true,
            ])
            ->add('vat', TextColumn::class, [
                'label'     => 'Taux de TVA',
                'orderable' => false,
                'render'    => function ($value, $prestation) {

                    if (null === $prestation->getVat()) {
                        return '';
                    }

                    return $prestation->getVat()->getValue() . ' %';
                }
            ])
        ;

        $datatableField
            ->addClinicField($table);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/medecine/include/_delete-button.html.twig', [
                'entity'         => 'medecine',
                'authorizations' => 'ADMIN_PRESTATION_DELETE'
            ])
            ->addOrderBy('title')
        ;

        $datatableField->createDatatableAdapter($table, Prestation::class, 'a.type = :medecine');

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/medecine/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_PRESTATION_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $prestation = new Prestation();

        $form = $this->createForm(PrestationType::class, $prestation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ttc = $prestation->getPriceHT() + ($prestation->getPriceHT() * $prestation->getVat()->getValue() / 100);
            $prestation
                ->setPriceTTC(round($ttc, 2))
                ->setCode(strtoupper(str_replace(' ', '_', $prestation->getCode())))
                ->setType('medecine')
            ;

            $this->entityManager->persist($prestation);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_medecine_index');
        }

        return $this->render('admin/medecine/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_PRESTATION_EDIT', prestation)")
     *
     * @param Request $request
     * @param Prestation $prestation
     * @return Response
     */
    public function edit(Request $request, Prestation $prestation): Response
    {
        $form = $this->createForm(PrestationType::class, $prestation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ttc = $prestation->getPriceHT() + ($prestation->getPriceHT() * $prestation->getVat()->getValue() / 100);
            $prestation
                ->setPriceTTC(round($ttc, 2))
                ->setCode(strtoupper(str_replace(' ', '_', $prestation->getCode())))
                ->setType('medecine')
            ;

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_medecine_index');
        }

        return $this->render('admin/medecine/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @Security("is_granted('ADMIN_PRESTATION_DELETE', prestation)")
     *
     * @param Prestation $prestation
     *
     * @return Response
     */
    public function delete(Prestation $prestation): Response
    {
        if (!$prestation instanceof Prestation) {
            return new JsonResponse([
                'type'    => 'error',
                'message' => 'Une erreur est survenue'
            ], 404);
        }

        $this->entityManager->remove($prestation);
        $this->entityManager->flush();

        return new JsonResponse([
            'type'    => 'success',
            'message' => 'Le médicament a été supprimé avec succès'
        ], 200);
    }
}