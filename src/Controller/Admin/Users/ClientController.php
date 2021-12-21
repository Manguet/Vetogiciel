<?php

namespace App\Controller\Admin\Users;

use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Service\User\PasswordEncoderServices;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Client\ClientFormType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/user", name="admin_user_")
 *
 * @Security("is_granted('ADMIN_CLIENT_ACCESS')")
 */
class ClientController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private PasswordEncoderServices $encoderServices;

    /**
     * ClientController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoderServices $encoderServices
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderServices $encoderServices)
    {
        $this->entityManager   = $entityManager;
        $this->encoderServices = $encoderServices;
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
            ->addFieldWithEditField($table, 'lastname',
                'Nom',
                'user',
                'ADMIN_CLIENT_SHOW',
                true
            )
            ->add('firstname', TextColumn::class, [
                'label'     => 'Prénom',
                'orderable' => true,
            ])
            ->add('clinic', TextColumn::class, [
                'label'     => 'Clinique',
                'orderable' => true,
                'render'    => function ($value, $client) {

                    if ($client->getClinic()->isEmpty()) {
                        return '';
                    }
                    foreach ($client->getClinic() as $clinic) {
                        $clinics[] = $clinic->getName();
                    }
                    return implode(', ', $clinics ?? []);
                }
            ])
            ->add('lastVisit', DateTimeColumn::class, [
                'label'     => 'Dernière visite le',
                'format'    => 'd/m/Y',
                'orderable' => true,
            ])
            ->add('animals', TextColumn::class, [
                'label'     => 'Nombre d\'animaux',
                'render'    => function ($value, $client) {
                    return count($client->getAnimals());
                }
            ]);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/user/include/_delete-button.html.twig', [
                'entity'         => 'client',
                'authorizations' => 'ADMIN_CLIENT_DELETE'
            ])
            ->addOrderBy('lastname')
        ;

        $datatableField->createDatatableAdapter($table, Client::class);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/user/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CLIENT_ADD')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newClient(Request $request): Response
    {
        $client = new Client();

        $form = $this->createForm(ClientFormType::class, $client, [
            'enablePassword' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $client);

            if (isset($request->request->get('client_form')['roles'])) {
                $client->setRoles([$request->request->get('client_form')['roles']]);
            }

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user_show', [
                'id' => $client->getId(),
            ]);
        }

        return $this->render('admin/user/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => true,
        ]);
    }

    /**
     * @Route("/show/{id}", name="show", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CLIENT_SHOW', client)")
     *
     * @param Client $client
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param DatatableFieldInterface $datatableField
     *
     * @return Response
     */
    public function show(Client $client, Request $request, DataTableFactory $dataTableFactory,
                         DatatableFieldInterface $datatableField): Response
    {
        $table = $dataTableFactory->create();

        $table
            ->add('name', TextColumn::class, [
                'label'     => 'Nom',
                'orderable' => true,
                'render'    => function ($value, $animal) {
                    return '<a href="/admin/animal/edit/' . $animal->getClient()->getId() . '/animal/' . $animal->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('age', TextColumn::class, [
                'label'     => 'Age',
                'orderable' => true,
            ])
            ->add('species', TextColumn::class, [
                'label'     => 'Espèce',
                'orderable' => false,
                'render'    => function ($value, $animal) {

                    if (null !== $animal->getSpecies()) {

                        return $animal->getSpecies()->getName();
                    }

                    return '';
                }
            ])
            ->add('race', TextColumn::class, [
                'label'     => 'Race',
                'orderable' => false,
                'render'    => function ($value, $animal) {

                    if (null !== $animal->getRace()) {

                        return $animal->getRace()->getName();
                    }

                    return '';
                }
            ])
            ->add('isAlive', BoolColumn::class, [
                'label'      => 'Vivant ?',
                'orderable'  => true,
                'trueValue'  => '',
                'falseValue' => 'Décédé',
                'nullValue'  => '',
            ])
        ;

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/animal/include/_delete-button.html.twig', [
                'entity'         => 'animal',
                'authorizations' => 'ADMIN_ANIMAL_DELETE'
            ])
            ->addOrderBy('name')
        ;

        $table
            ->createAdapter(ORMAdapter::class, [
                'entity' => Animal::class,
                'query'  => function (QueryBuilder $builder) use ($client){
                    $builder
                        ->select('a')
                        ->from(Animal::class, 'a')
                        ->where('a.client = :id')
                        ->setParameter('id', $client->getId())
                    ;
                }
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/user/show.html.twig', [
            'client' => $client,
            'table'  => $table,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ADMIN_CLIENT_EDIT', client)")
     *
     * @param Client $client
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Client $client, Request $request): Response
    {
        $form = $this->createForm(ClientFormType::class, $client, [
            'enablePassword' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (isset($request->request->get('client_form')['roles'])) {
                $client->setRoles([$request->request->get('client_form')['roles']]);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user_show', [
                'id' => $client->getId(),
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'client'         => $client,
            'form'           => $form->createView(),
            'enablePassword' => false,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_CLIENT_DELETE', client)")
     *
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function delete(Client $client): JsonResponse
    {
        if (!$client instanceof Client) {
            return new JsonResponse('Client Not Found', 404);
        }

        foreach ($client->getAnimals() as $animal) {
            $this->entityManager->remove($animal);
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return new JsonResponse('Client deleted with success', 200);
    }
}