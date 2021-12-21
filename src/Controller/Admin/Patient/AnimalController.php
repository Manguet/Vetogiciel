<?php

namespace App\Controller\Admin\Patient;

use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use App\Form\Animal\AnimalType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Service\Dates\DateServices;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omines\DataTablesBundle\Column\BoolColumn;
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
 * @Route("/admin/animal", name="admin_animal_")
 *
 * @Security("is_granted('ADMIN_ANIMAL_ACCESS')")
 */
class AnimalController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private DateServices $dateServices;

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
            ->addFieldWithEditField($table, 'name',
                'Nom de l\'animal',
                'animal',
                'ADMIN_ANIMAL_EDIT'
            )
            ->add('client', TextColumn::class, [
                'label'     => 'Propriétaire',
                'orderable' => false,
                'render'    => function ($value, $animal) {

                    if (null === $animal->getClient()) {
                        return '';
                    }
                    return $animal->getClient()->getFirstName() . ' ' . $animal->getClient()->getLastName();
                }
            ])
            ->add('age', TextColumn::class, [
                'label'     => 'Age de l\'animal',
                'orderable' => true,
                'render'    => function ($value) {

                    if (empty($value)) {
                        return '';
                    }

                    $age = ' an';

                    if ($value > 1) {
                        $age = ' ans';
                    }

                    return $value . $age;
                }
            ])
            ->add('isInsured', BoolColumn::class, [
                'label'      => 'Assuré ?',
                'orderable'  => true,
                'trueValue'  => 'Assuré',
                'falseValue' => '',
                'nullValue'  => '',
            ])
            ->add('isAlive', BoolColumn::class, [
                'label'      => 'Vivant ?',
                'orderable'  => true,
                'trueValue'  => '',
                'falseValue' => 'Décédé',
                'nullValue'  => '',
            ])
            ->add('race', TextColumn::class, [
                'visible' => false,
            ])
            ->add('species', TextColumn::class, [
                'label'     => 'Espèce',
                'orderable' => false,
                'render'    => function ($value, $animal) {

                    $race = $specie = '';

                    if (null !== $animal->getRace()) {
                        $race = $animal->getRace()->getName();
                    }

                    if (null !== $animal->getSpecies()) {
                        $specie = $animal->getSpecies()->getName();
                    }

                    if (!empty($race) && !empty($specie)) {
                        return $race . ' ( ' . $specie . ' ) ';
                    }

                    return $race . $specie;
                }
            ])
        ;

        $datatableField
            ->addDeleteField($table, 'admin/animal/include/_delete-button.html.twig', [
                'entity'         => 'animal',
                'authorizations' => 'ADMIN_ANIMAL_DELETE'
            ])
            ->addOrderBy('name')
        ;

        $datatableField->createDatatableAdapter($table, Animal::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/animal/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new_alone", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_ANIMAL_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function newWithoutClient(Request $request): Response
    {
        $animal = new Animal();

        $form = $this->createForm(AnimalType::class, $animal, [
            'isNew' => true,
            'alone' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** If new species add race if exist */
            if (null !== $animal->getRace() && null !== $animal->getSpecies()) {
                $animal->getSpecies()->setRace($animal->getRace());
            }

            /** Calculate age or birthday */
            $this->dateServices->calculateAges($animal);

            $this->entityManager->persist($animal);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_animal_index');
        }

        return $this->render('admin/animal/new.html.twig', [
            'form'  => $form->createView(),
            'alone' => true,
        ]);
    }

    /**
     * @Route("/new/{id}", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_ANIMAL_ADD')")
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

        $form = $this->createForm(AnimalType::class, $animal, [
            'isNew' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animal->setClient($client);

            /** If new species add race if exist */
            if (null !== $animal->getRace() && null !== $animal->getSpecies()) {
                $animal->getSpecies()->setRace($animal->getRace());
            }

            /** Calculate age or birthday */
            $this->dateServices->calculateAges($animal);

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
     * @Route("/edit/{id}/animal/{animal}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_ANIMAL_EDIT', animal)")
     *
     * @param Client $client
     * @param Animal $animal
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Client $client, Animal $animal ,Request $request): Response
    {
        $form = $this->createForm(AnimalType::class, $animal, [
            'isNew' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animal->setClient($client);

            /** If new species add race if exist */
            if (null !== $animal->getRace() && null !== $animal->getSpecies()) {
                $animal->getSpecies()->setRace($animal->getRace());
            }

            /** Calculate age or birthday */
            $this->dateServices->calculateAges($animal);

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
     * @Route("/edit/{id}", name="edit_alone", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_ANIMAL_EDIT', animal)")
     *
     * @param Animal $animal
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function editAlone(Animal $animal, Request $request): Response
    {
        $form = $this->createForm(AnimalType::class, $animal, [
            'isNew' => false,
            'alone' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** If new species add race if exist */
            if (null !== $animal->getRace() && null !== $animal->getSpecies()) {
                $animal->getSpecies()->setRace($animal->getRace());
            }

            /** Calculate age or birthday */
            $this->dateServices->calculateAges($animal);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_animal_index');
        }

        return $this->render('admin/animal/new.html.twig', [
            'form'  => $form->createView(),
            'alone' => true,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_ANIMAL_DELETE', animal)")
     *
     * @param Animal $animal
     *
     * @return JsonResponse
     */
    public function delete(Animal $animal): JsonResponse
    {
        if (!$animal instanceof Animal) {
            return new JsonResponse([
                'type'    => 'error',
                'message' => 'Une erreur est survenue'
            ], 404);
        }

        try {
            $this->entityManager->remove($animal);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return new JsonResponse([
                'type'    => 'error',
                'message' => 'Impossible de supprimer cet animal qui a un rendez-vous'
            ], 200);
        }

        return new JsonResponse([
            'type'    => 'success',
            'message' => 'L\'animal a bien été supprimé'
        ], 200);
    }
}