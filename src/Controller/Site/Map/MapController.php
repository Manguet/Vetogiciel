<?php

namespace App\Controller\Site\Map;

use App\Entity\Settings\Configuration;
use App\Entity\Structure\Clinic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/access/{clinicName}", name="map_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class MapController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="access")
     *
     * @param string $clinicName
     *
     * @return Response
     */
    public function clinicAccess(string $clinicName): Response
    {
        $mapCounter = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => 'map_counter']);

        $mapCounterMax = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => 'map_max_counter']);

        if (!$mapCounter || !$mapCounterMax) {
            throw $this->createAccessDeniedException();
        }

        if(date('j') === '1') {
            $mapCounter->setDatas(['values' => 0]);
            $this->entityManager->flush();
        }

        if ($mapCounter->getDatas()['values'] >= $mapCounterMax->getDatas()['values']) {
            throw $this->createAccessDeniedException();
        }

        $mapCounter->setDatas(['values' => ($mapCounter->getDatas()['values'] + 1)]);
        $this->entityManager->flush();

        $clinic = $this->entityManager->getRepository(Clinic::class)
            ->findOneBy(['nameSlugiffied' => $clinicName]);

        if (!$clinic) {
            throw $this->createAccessDeniedException();
        }

        if (null === $clinic->getLongitude() || null === $clinic->getLatitude()) {
            throw $this->createAccessDeniedException();
        }

        $clinics = $this->entityManager->getRepository(Clinic::class)
            ->findAll();

        return $this->render('site/clinic/access.html.twig', [
            'clinic'  => $clinic,
            'clinics' => $clinics,
        ]);
    }
}