<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\JobOffer;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
use App\Service\Priority\PriorityServices;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/joboffer", name="admin_joboffer_")
 *
 * @Security("is_granted('ADMIN_JOBOFFER_ACCESS')")
 */
class JobOfferController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private PriorityServices $priorityServices;

    private SluggerInterface $slugger;

    /**
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
        $table = $dataTableFactory->create();

        $datatableField
            ->addFieldWithEditField($table, 'title',
                'Titre',
                'joboffer',
                'ADMIN_JOBOFFER_EDIT'
            )
            ->add('type', TextColumn::class, [
                'label'     => 'Catégorie',
                'orderable' => false,
                'render'    => function ($value, $jobOffer) {

                    if ($jobOffer->getType()) {
                        return $jobOffer->getType()->getName();
                    }
                    return '';
                }
            ]);

        $datatableField
            ->addClinicField($table)
            ->add('isActivated', TextColumn::class, [
                'label'     => 'Offre active ?',
                'orderable' => true,
                'render'    => function ($value, $jobOffer) {
                    if ($jobOffer->getIsActivated()) {
                        return 'Actif';
                    }
                    return 'Désactivé';
                }
            ])
            ->add('priority', TextColumn::class, [
                'label'     => 'Priorité d\'affichage',
                'orderable' => true,
            ]);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/content/joboffer/include/_delete-button.html.twig', [
                'entity'         => 'joboffer',
                'authorizations' => 'ADMIN_JOBOFFER_DELETE'
            ])
            ->addOrderBy('priority')
        ;

        $datatableField
            ->createDatatableAdapter($table, JobOffer::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/content/joboffer/index.html.twig', [
            'table' => $table,
        ]);
    }
}