<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\JobOfferType;
use App\Form\Content\JobOfferCategoryType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
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
 * @Route("/admin/joboffer-type", name="admin_joboffer_type_")
 *
 * @Security("is_granted('ADMIN_JOBOFFERTYPE_ACCESS')")
 */
class JobOfferTypeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SluggerInterface $slugger;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger       = $slugger;
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
                'Titre de la catégorie',
                'joboffer-type',
                'ADMIN_JOBOFFERTYPE_EDIT'
            )
            ->add('jobOffers', TextColumn::class, [
                'label'     => 'Nombre d\'offres',
                'orderable' => true,
                'render'    => function ($value, $context) {
                    if (!$context->getJobOffers()->isEmpty()) {
                        return count($context->getJobOffers());
                    }

                    return '0';
                }
            ])
    ;

        $datatableField
            ->addDeleteField($table, 'admin/content/joboffer/type/include/_delete-button.html.twig', [
                'entity'         => 'joboffertype',
                'authorizations' => 'ADMIN_JOBOFFERTYPE_DELETE'
            ])
            ->addOrderBy('title')
        ;

        $datatableField
            ->createDatatableAdapter($table, JobOfferType::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/content/joboffer/type/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_JOBOFFERTYPE_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newCategory(Request $request): Response
    {
        $category = new JobOfferType();

        $form = $this->createForm(JobOfferCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setTitleUrl(
                $this->slugger->generateSlugUrl(
                    $category->getTitle(), JobOfferType::class
                )
            );

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_joboffer_type_index');
        }

        return $this->render('admin/content/joboffer/type/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_JOBOFFERTYPE_EDIT', category)")
     *
     * @param JobOfferType $category
     * @param Request $request
     *
     * @return Response
     */
    public function edit(JobOfferType $category, Request $request): Response
    {
        $form = $this->createForm(JobOfferCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_joboffer_type_index');
        }

        return $this->render('admin/content/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_JOBOFFERTYPE_DELETE', category)")
     *
     * @param JobOfferType $category
     *
     * @return JsonResponse
     */
    public function delete(JobOfferType $category): JsonResponse
    {
        if (!$category instanceof JobOfferType) {
            return new JsonResponse('Category Not Found', 404);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new JsonResponse('Category deleted with success', 200);
    }
}