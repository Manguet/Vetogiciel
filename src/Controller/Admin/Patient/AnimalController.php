<?php

namespace App\Controller\Admin\Patient;

use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use App\Form\Animal\AnimalType;
use App\Service\Dates\DateServices;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/animal", name="admin_animal_")
 */
class AnimalController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DateServices
     */
    private $dateServices;

    /**
     * @param EntityManagerInterface $entityManager
     * @param DateServices $dateServices
     */
    public function __construct(EntityManagerInterface $entityManager, DateServices $dateServices)
    {
        $this->entityManager = $entityManager;
        $this->dateServices  = $dateServices;
    }

    /**
     * @Route("/new/{id}", name="new", methods={"GET", "POST"})
     *
     * @param Client $client
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Response
     */
    public function new(Client $client, Request $request): Response
    {
        $animal = new Animal();

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animal->setClient($client);

            /** If new species add race if exist */
            if (null !== $animal->getRace() && null !== $animal->getSpecies()) {
                $animal->getSpecies()->setRace($animal->getRace());
            }

            /** Calculate age or birthday */
            $this->calculateAges($animal);

            $this->entityManager->persist($animal);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user_show', [
                'id' => $client->getId(),
            ]);
        }

        return $this->render('admin/animal/new.html.twig', [
            'client' => $client,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @param Animal $animal
     *
     * @throws Exception
     *
     * @return void
     */
    private function calculateAges(Animal $animal): void
    {
        if (null !== $animal->getBirthdate()) {
            $actualDate = $this->dateServices->getCurrentDateObject();

            $interval = $actualDate->diff($animal->getBirthdate())->y;
            $animal->setAge($interval);

        } elseif (null !== $animal->getAge()) {
            $actualDate = $this->dateServices->getCurrentDateObject();

            $interval = new DateInterval('P' . $animal->getAge() . 'Y');
            $date = $actualDate->sub($interval);

            $animal->setBirthdate($date);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Animal $animal
     *
     * @return JsonResponse
     */
    public function delete(Animal $animal): JsonResponse
    {
        if (!$animal instanceof Animal) {
            return new JsonResponse('Animal Not Found', 404);
        }

        $this->entityManager->remove($animal);
        $this->entityManager->flush();

        return new JsonResponse('Animal deleted with success', 200);
    }
}