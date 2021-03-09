<?php

namespace App\Controller\Admin\Ajax;

use App\Entity\Patients\Race;
use App\Entity\Patients\Species;
use App\Entity\Structure\Clinic;
use App\Entity\Structure\Sector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/ajax/", name="admin_ajax_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminAjaxController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AdminAjaxController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("race", name="race")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteRace(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $races = $this->entityManager->getRepository(Race::class)
        ->findAllByNameResults($query);

        $raceResults = [];
        foreach ($races as $race) {
            $raceResults[] = [
                'id'   => $race->getId(),
                'text' => $race->getName(),
            ];
        }

        return new JsonResponse($raceResults);
    }

    /**
     * @Route("species", name="species")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteSpecies(Request $request): JsonResponse
    {
        $query  = $request->get('q');
        $raceId = $request->get('race');

        $species = $this->entityManager->getRepository(Species::class)
            ->findAllByNameResults($query, $raceId);

        $speciesResults = [];
        foreach ($species as $specie) {
            $speciesResults[] = [
                'id'   => $specie->getId(),
                'text' => $specie->getName(),
            ];
        }

        return new JsonResponse($speciesResults);
    }

    /**
     * @Route("structure", name="structure")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteStruture(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $structures = $this->entityManager->getRepository(Clinic::class)
            ->findAllByNameResults($query);

        $structureResults = [];
        foreach ($structures as $structure) {
            $structureResults[] = [
                'id'   => $structure->getId(),
                'text' => $structure->getName(),
            ];
        }

        return new JsonResponse($structureResults);
    }

    /**
     * @Route("sector", name="sector")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteSector(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $sectors = $this->entityManager->getRepository(Sector::class)
            ->findAllByNameResults($query);

        $sectorResults = [];
        foreach ($sectors as $sector) {
            $sectorResults[] = [
                'id'   => $sector->getId(),
                'text' => $sector->getName(),
            ];
        }

        return new JsonResponse($sectorResults);
    }
}