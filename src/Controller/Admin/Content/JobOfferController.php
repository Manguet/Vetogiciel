<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\JobOffer;
use App\Form\Content\JobType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
use App\Service\Priority\PriorityServices;
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
                'ADMIN_JOBOFFER_SHOW',
                true
            )
            ->add('type', TextColumn::class, [
                'label'     => 'Catégorie',
                'orderable' => false,
                'render'    => function ($value, $jobOffer) {

                    if ($jobOffer->getType()) {
                        return $jobOffer->getType()->getTitle();
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

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_JOBOFFER_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newJobOffer(Request $request): Response
    {
        $job = new JobOffer();

        $form = $this->createForm(JobType::class, $job, [
            'joboffer' => null,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $priority = $this->priorityServices->setPriorityOnCreation($job);
            $job->setPriority($priority);

            $job->setTitleUrl(
                $this->slugger->generateSlugUrl(
                    $job->getTitle(), JobOffer::class
                )
            );

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_joboffer_index');
        }

        return $this->render('admin/content/joboffer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="show", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_JOBOFFER_SHOW', job)")
     *
     * @param JobOffer $job
     *
     * @return Response
     */
    public function show(JobOffer $job): Response
    {
        return $this->render('admin/content/joboffer/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_JOBOFFER_EDIT', job)")
     *
     * @param JobOffer $job
     * @param Request $request
     *
     * @return Response
     */
    public function edit(JobOffer $job, Request $request): Response
    {
        $form = $this->createForm(JobType::class, $job, [
            'joboffer' => $job,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_joboffer_index');
        }

        return $this->render('admin/content/joboffer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_JOBOFFER_DELETE', job)")
     *
     * @param JobOffer $job
     *
     * @return JsonResponse
     */
    public function delete(JobOffer $job): JsonResponse
    {
        if (!$job instanceof JobOffer) {
            return new JsonResponse('Job Offer Not Found', 404);
        }

        $this->entityManager->remove($job);
        $this->entityManager->flush();

        $jobs = $this->entityManager->getRepository(JobOffer::class)
            ->findByOfferPriority();

        $priority = 0;
        foreach ($jobs as $jobAfterUpdate) {
            $jobAfterUpdate->setPriority($priority);
            $priority++;
        }

        $this->entityManager->flush();

        return new JsonResponse('Job deleted with success', 200);
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
            $movedJobs = $request->get('movedJobs');
            $newJobs   = $request->get('newJobs');
            $jobs = $jobPositions = [];

            $i = 0;
            if (is_array($movedJobs) && is_array($newJobs)) {

                foreach ($movedJobs as $movedJob) {
                    $jobs[$movedJob] = $newJobs[$i];
                    $i++;
                }

                foreach ($jobs as $oldPosition => $newPosition) {

                    $job = $this->entityManager->getRepository(JobOffer::class)
                        ->findOneBy([
                            'priority' => (int)$oldPosition,
                        ])
                    ;

                    if ($job) {
                        $jobPositions[$job->getId()] = $newPosition;
                    }
                }

                foreach ($jobPositions as $id => $newPosition) {

                    $job = $this->entityManager->getRepository(JobOffer::class)
                        ->find((int)$id)
                    ;

                    $job->setPriority($newPosition);
                    $this->entityManager->persist($job);
                }

                $this->entityManager->flush();

                return new JsonResponse('Priorité mise à jour', 200);
            }
            return new JsonResponse('Pas de modification', 200);

        }

        return new Response('error', 500);
    }
}