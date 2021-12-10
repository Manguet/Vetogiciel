<?php

namespace App\Controller;

use App\Entity\Contents\Article;
use App\Entity\Structure\Clinic;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $sectors = $this->getSectorsByFour();

        $articles = $this->entityManager->getRepository(Article::class)
            ->findByMax(4);

        $veterinarians = $this->entityManager->getRepository(Veterinary::class)
            ->findBy([
                 'isVerified' => true,
            ]);

        return $this->render('index.html.twig', [
            'sectors'       => $sectors,
            'articles'      => $articles,
            'veterinarians' => $veterinarians,
        ]);
    }

    /**
     * Generate header for site
     *
     * @param Request $request
     *
     * @return Response
     */
    public function headerAction(Request $request): Response
    {
        $clinics = $this->entityManager->getRepository(Clinic::class)
            ->findClinicsByPriority();

        $response = new Response();

        $selectedClinic = null;

        if ($clinics && $request->cookies->get('selectedClinic')) {

            $selectedClinic = $this->entityManager->getRepository(Clinic::class)
                ->find((int)$request->cookies->get('selectedClinic'));
        }

        if ($clinics && !$request->cookies->get('selectedClinic')) {
            $selectedClinic = $clinics[0];

            $response->headers->setCookie(new Cookie('selectedClinic', $selectedClinic->getId()));
        }

        $content = $this->renderView('base/_navbar.html.twig', [
            'clinics'        => $clinics,
            'selectedClinic' => $selectedClinic,
        ]);

        $response->setContent($content);

        return $response;
    }

    /**
     * @return array of sectors grouped by 4
     */
    private function getSectorsByFour(): array
    {
        $sectors = $this->entityManager->getRepository(Sector::class)->findAll();

        $sectorsByThree = [];
        $i = $j = 0;
        foreach ($sectors as $sector) {
            if ($i % 4 === 0) {
                $j++;
            }
            $sectorsByThree[$j][] = $sector;

            $i++;
        }

        return $sectorsByThree;
    }
}
