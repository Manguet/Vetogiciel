<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Settings\Authorization;
use App\Form\Settings\AuthorizationType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
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
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('relatedEntity', TextColumn::class, [
                'label'     => 'Entité en relation',
                'orderable' => true,
                'render'    => function ($value, $authorization) {
                    return '<a href="/admin/authorization/edit/' . $authorization->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('canAccess', TextColumn::class, [
                'label'     => 'Accès',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }
                }
            ])
            ->add('canAdd', TextColumn::class, [
                'label'     => 'Création',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }
                }
            ])
            ->add('canShow', TextColumn::class, [
                'label'     => 'Voir en détail',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }
                }
            ])
            ->add('canEdit', TextColumn::class, [
                'label'     => 'Edition',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }
                }
            ])
            ->add('canDelete', TextColumn::class, [
                'label'     => 'Suppression',
                'orderable' => true,
                'render'    => function ($value) {
                    if (!empty($value)) {
                        return '<i class="fas fa-check"></i>';
                    }
                }
            ])
            ->add('delete', TextColumn::class, [
                'label'   => 'Supprimer ?',
                'render'  => function($value, $authorization) {
                    return $this->renderView('admin/settings/authorizations/include/_delete-button.html.twig', [
                        'authorization' => $authorization,
                    ]);
                }
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