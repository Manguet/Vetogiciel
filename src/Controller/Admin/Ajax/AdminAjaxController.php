<?php

namespace App\Controller\Admin\Ajax;

use App\Entity\Contents\ArticleCategory;
use App\Entity\Contents\JobOfferType;
use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use App\Entity\Patients\Race;
use App\Entity\Patients\Species;
use App\Entity\Settings\Role;
use App\Entity\Structure\Clinic;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Vat;
use App\Entity\Structure\Veterinary;
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
    private EntityManagerInterface $entityManager;

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

    /**
     * @Route("article-category", name="article_category")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteArticleCategory(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $categories = $this->entityManager->getRepository(ArticleCategory::class)
            ->findAllByTitleResults($query);

        $categoryResults = [];
        foreach ($categories as $category) {
            $categoryResults[] = [
                'id'   => $category->getId(),
                'text' => $category->getTitle(),
            ];
        }

        return new JsonResponse($categoryResults);
    }

    /**
     * @Route("offer-category", name="joboffer_category")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteOfferCategory(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $categories = $this->entityManager->getRepository(JobOfferType::class)
            ->findAllByTitleResults($query);

        $categoryResults = [];
        foreach ($categories as $category) {
            $categoryResults[] = [
                'id'   => $category->getId(),
                'text' => $category->getTitle(),
            ];
        }

        return new JsonResponse($categoryResults);
    }

    /**
     * @Route("veterinary", name="veterinary")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteVeterinary(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $veterinaries = $this->entityManager->getRepository(Veterinary::class)
            ->findAllByNameResults($query);

        $veterinaryResults = [];
        foreach ($veterinaries as $veterinary) {
            $veterinaryResults[] = [
                'id'   => $veterinary->getId(),
                'text' => $veterinary->getLastName() . ' ' . $veterinary->getFirstName(),
            ];
        }

        return new JsonResponse($veterinaryResults);
    }

    /**
     * @Route("client", name="client")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteClient(Request $request): JsonResponse
    {
        $query        = $request->get('q');
        $veterinaryId = $request->get('veterinary');

        if ($veterinaryId) {
            $veterinary   = $this->entityManager->getRepository(Veterinary::class)
                ->find($veterinaryId);

            $clients = $this->entityManager->getRepository(Client::class)
                ->findAllByNameResults($query, $veterinary->getClinic());

        } else {
            $clients = $this->entityManager->getRepository(Client::class)
                ->findAllByNameResults($query, $this->getUser()->getClinic());
        }

        $clientResults = [];
        foreach ($clients as $client) {
            $clientResults[] = [
                'id'   => $client->getId(),
                'text' => $client->getLastName() . ' ' . $client->getFirstName(),
            ];
        }

        return new JsonResponse($clientResults);
    }

    /**
     * @Route("animal", name="animal")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteAnimal(Request $request): JsonResponse
    {
        $query        = $request->get('q');
        $client       = $request->get('client');

        $animals = $this->entityManager->getRepository(Animal::class)
            ->findAllByNameResults($query, $client);

        $animalResults = [];
        foreach ($animals as $animal) {
            $animalResults[] = [
                'id'   => $animal->getId(),
                'text' => $animal->getName(),
            ];
        }

        return new JsonResponse($animalResults);
    }

    /**
     * @Route("employee", name="employee")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteEmployee(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $employees = $this->entityManager->getRepository(Employee::class)
            ->findAllByNameResults($query);

        $employeeResults = [];
        foreach ($employees as $employee) {
            $employeeResults[] = [
                'id'   => $employee->getId(),
                'text' => $employee->getLastName() . ' ' . $employee->getFirstName(),
            ];
        }

        return new JsonResponse($employeeResults);
    }

    /**
     * @Route("role", name="role")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteRole(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $roles = $this->entityManager->getRepository(Role::class)
            ->findAllByNameResults($query);

        $roleResults = [];
        foreach ($roles as $role) {
            $roleResults[] = [
                'id'   => $role->getId(),
                'text' => $role->getName(),
            ];
        }

        return new JsonResponse($roleResults);
    }

    /**
     * @Route("vat", name="vat")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteVat(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $vats = $this->entityManager->getRepository(Vat::class)
            ->findAllByNameResults($query);

        $vatResults = [];
        foreach ($vats as $vat) {
            $vatResults[] = [
                'id'   => $vat->getId(),
                'text' => $vat->getValue() . '%',
            ];
        }

        return new JsonResponse($vatResults);
    }
}