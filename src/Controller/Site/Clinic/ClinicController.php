<?php

namespace App\Controller\Site\Clinic;

use App\Entity\Structure\Clinic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/clinic", name="clinic_")
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
     * @Route("/change/{clinic}", name="change")
     *
     * @param string $clinic
     *
     * @return RedirectResponse
     */
    public function changeClinic(string $clinic): RedirectResponse
    {
        $response = new RedirectResponse('/');

        $response->headers->setCookie(new Cookie('selectedClinic', $clinic));

        return $response;
    }

    /**
     * @Route("/{clinic}", name="show")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function show(Request $request): Response
    {
        $clinicName = $request->attributes->get('clinic');

        $clinic = $this->entityManager->getRepository(Clinic::class)
            ->findOneBy(['nameSlugiffied' => $clinicName]);

        return $this->render('site/clinic/show.html.twig', [
            'clinic' => $clinic,
        ]);
    }
}