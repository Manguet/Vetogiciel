<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Settings\Authorization;
use App\Form\Settings\AuthorizationType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/authorization/", name="authorization_")
 *
 * @Security("is_granted('ADMIN_AUTHORIZATION_ACCESS')")
 */
class AuthorizationsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->kernel        = $kernel;
    }

    /**
     * @Route ("index", name="index")
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
            ->addFieldWithEditField($table, 'relatedEntity',
                'Entité en relation',
                'authorization',
                'ADMIN_AUTHORIZATION_EDIT'
            )
            ->add('canAccess', TextColumn::class, [
                'label'     => 'Accès',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }

                    return '';
                }
            ])
            ->add('canAdd', TextColumn::class, [
                'label'     => 'Création',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }

                    return '';
                }
            ])
            ->add('canShow', TextColumn::class, [
                'label'     => 'Voir en détail',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }

                    return '';
                }
            ])
            ->add('canEdit', TextColumn::class, [
                'label'     => 'Edition',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }

                    return '';
                }
            ])
            ->add('canDelete', TextColumn::class, [
                'label'     => 'Suppression',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }

                    return '';
                }
            ]);

        $datatableField
            ->addDeleteField($table, 'admin/settings/authorizations/include/_delete-button.html.twig', [
                'entity' => 'authorization'
            ])
            ->addOrderBy('relatedEntity')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Authorization::class
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/settings/authorizations/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route ("edit/{id}", name="edit")
     * @Security("is_granted('ADMIN_AUTHORIZATION_EDIT', authorization)")
     *
     * @param Request $request
     * @param Authorization $authorization
     *
     * @return Response
     */
    public function edit(Request $request, Authorization $authorization): Response
    {
        $form = $this->createForm(AuthorizationType::class, $authorization, [
            'entities' => $this->getEntities()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('authorization_index');
        }

        return $this->render('admin/settings/authorizations/new.html.twig', [
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ADMIN_AUTHORIZATION_DELETE', authorization)")
     *
     * @param Authorization $authorization
     *
     * @return JsonResponse
     */
    public function delete(Authorization $authorization): JsonResponse
    {
        if (!$authorization instanceof Authorization) {
            return new JsonResponse('Authorization Not Found', 404);
        }

        $this->entityManager->remove($authorization);
        $this->entityManager->flush();

        return new JsonResponse('Authorization deleted with success', 200);
    }

    /**
     * @Route("generate", name="generate")
     * @Security("is_granted('ADMIN_AUTHORIZATION_ADD')")
     *
     * @param KernelInterface $kernel
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function generate(KernelInterface $kernel): JsonResponse
    {
        $command = new Application($kernel);
        $command->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'import:authorizations',
        ]);

        $output = new NullOutput();
        $command->run($input, $output);

        return new JsonResponse('Import des autorizations effectué');
    }

    /**
     * @return array
     */
    private function getEntities(): array
    {
        $finder = new Finder();

        $finder->in($this->kernel->getProjectDir() . '/src/Entity');

        if (!$finder->hasResults()) {
            return [];
        }

        foreach ($finder as $entity) {

            $entityName = str_replace('.php', '', $entity->getFilename());
            $entities[$entityName] = $entityName;
        }

        asort($entities);

        return $entities;
    }
}