<?php

namespace App\Controller\Site\Presentations;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/presentation", name="presentation_")
 */
class PresentationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PresentationController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="index")
     */
    public function index()
    {
        $veterinarians = $this->entityManager->getRepository(Veterinary::class)
            ->findBy([
                'isVerified' => true,
            ]);

        $employees = $this->entityManager->getRepository(Employee::class)
            ->findBy([
                'isVerified' => true,
            ]);

        return $this->render('site/presentation/index.html.twig', [
            'veterinarians' => $veterinarians,
            'employees'     => $employees,
        ]);
    }

    /**
     * @Route("/{fullName}", name="show")
     *
     * @param string $fullName
     *
     * @return Response
     */
    public function show(string $fullName): Response
    {
        $worker = $this->entityManager->getRepository(Veterinary::class)
            ->findOneBy([
                'fullNameSlugiffied' => $fullName,
            ]);

        $isVeterinary = true;

        if (!$worker) {

            $worker = $this->entityManager->getRepository(Employee::class)
                ->findOneBy([
                    'fullNameSlugiffied' => $fullName,
                ]);

            $isVeterinary = false;
        }

        if (!$worker) {
            throw $this->createNotFoundException('404');
        }

        return $this->render('site/presentation/show.html.twig', [
            'worker'       => $worker,
            'isVeterinary' => $isVeterinary,
        ]);
    }

    /**
     * @Route("/cv/{worker}", name="cv")
     *
     * @param Request $request
     * @param Veterinary|Employee $worker
     *
     * @return BinaryFileResponse
     */
    public function downloadCv(Request $request, $worker): BinaryFileResponse
    {
        $class = Employee::class;

        if ($request->get('isVeterinary')) {
            $class = Veterinary::class;
        }

        $workerClass = $this->entityManager->getRepository($class)
            ->find((int)$worker);

        if (!$workerClass) {
            $this->createNotFoundException('404');
        }

        $fileName  = $workerClass->getCvFile()->getFilename();
        $filePath  = $workerClass->getCvFile()->getPathname();

        $extension = substr($fileName, -3);

        $response = new BinaryFileResponse($filePath);

        if ($extension === 'pdf') {
            $response->headers->set('Content-Type','application/pdf');
        } else {
            $response->headers->set('Content-Type','image/' . $extension);
        }

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}