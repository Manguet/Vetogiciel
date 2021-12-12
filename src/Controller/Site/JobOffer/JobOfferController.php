<?php

namespace App\Controller\Site\JobOffer;

use App\Entity\Contents\Candidate;
use App\Entity\Contents\JobOffer;
use App\Entity\Contents\JobOfferType;
use App\Entity\Structure\Clinic;
use App\Form\Site\CandidateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("recrutement/{clinicName}", name="job_")
 */
class JobOfferController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="index")
     *
     * @param string $clinicName
     *
     * @return Response
     */
    public function index(string $clinicName): Response
    {
        $clinic = $this->entityManager->getRepository(Clinic::class)
            ->findOneBy(['nameSlugiffied' => $clinicName]);

        if (null === $clinic) {
            throw $this->createNotFoundException();
        }

        $jobs = $this->entityManager->getRepository(JobOffer::class)
            ->findBy(['clinic' => $clinic, 'isActivated' => true]);

        $categories = $this->entityManager->getRepository(JobOfferType::class)
            ->findAll();

        return $this->render('site/joboffer/index.html.twig', [
            'clinicName' => $clinicName,
            'jobs'       => $jobs,
            'categories' => $categories,
            'clinic'     => $clinic
        ]);
    }

    /**
     * @Route("/{category}", name="show")
     *
     * @param Request $request
     * @param string $clinicName
     *
     * @return Response
     */
    public function show(Request $request, string $clinicName): Response
    {
        $jobCategory = $this->entityManager->getRepository(JobOfferType::class)
            ->findOneBy(['titleUrl' => $request->attributes->get('category')]);

        if (!$jobCategory) {
            $this->createNotFoundException('404');
        }

        $categories = $this->entityManager->getRepository(JobOfferType::class)
            ->findAll();

        $clinic = $this->entityManager->getRepository(Clinic::class)
            ->findOneBy(['nameSlugiffied' => $clinicName]);

        if (null === $clinic) {
            throw $this->createNotFoundException();
        }

        return $this->render('site/joboffer/show.html.twig', [
            'category'   => $jobCategory,
            'categories' => $categories,
            'clinicName' => $clinicName,
            'clinic'     => $clinic
        ]);
    }

    /**
     * @Route("/{category}/{job}", name="show_details")
     *
     * @param Request $request
     * @param string $clinicName
     *
     * @return Response
     */
    public function showInDetails(Request $request, string $clinicName): Response
    {
        $candidate = new Candidate();

        $clinic = $this->entityManager->getRepository(Clinic::class)
            ->findOneBy(['nameSlugiffied' => $clinicName]);

        if (null === $clinic) {
            throw $this->createNotFoundException();
        }

        $job = $this->entityManager->getRepository(JobOffer::class)
            ->findOneBy([
                'titleUrl' => $request->attributes->get('job'),
                'clinic'   => $clinic
            ]);

        if (!$job) {
            throw $this->createNotFoundException('404');
        }

        $categories = $this->entityManager->getRepository(JobOfferType::class)
            ->findAll();

        $form = $this->createForm(CandidateType::class, $candidate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $candidate->setJoboffer($job);

            $this->entityManager->persist($candidate);
            $this->entityManager->flush();

            $this->addFlash('success', 'Vous avez bien postuler pour l\'offre : ' . $job->getTitle() . '.');

            return $this->redirectToRoute('index');
        }

        return $this->render('site/joboffer/show_details.html.twig', [
            'job'         => $job,
            'form'        => $form->createView(),
            'categories'  => $categories,
            'clinicName'  => $clinicName
        ]);
    }
}