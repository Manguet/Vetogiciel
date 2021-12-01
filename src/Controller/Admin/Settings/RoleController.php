<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Settings\Authorization;
use App\Entity\Settings\Role;
use App\Form\Settings\RoleType;
use App\Interfaces\Settings\RoleTableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/role/", name="role_")
 */
class RoleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RoleTableInterface
     */
    private $roleTable;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RoleTableInterface $roleTable
     */
    public function __construct(EntityManagerInterface $entityManager, RoleTableInterface $roleTable)
    {
        $this->entityManager = $entityManager;
        $this->roleTable     = $roleTable;
    }

    /**
     * @Route("", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('name', TextColumn::class, [
                'label'     => 'Nom du rôle',
                'orderable' => true,
                'render'    => function ($value, $role) {
                    return '<a href="/admin/role/edit/' . $role->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('parentRole', TextColumn::class, [
                'label'     => 'Rôle Parent',
                'orderable' => true,
            ])
            ->add('childRole', TextColumn::class, [
                'label'     => 'Rôles Enfants',
                'orderable' => true,
                'render'    => function ($value) {
                    if (empty($value)) {
                        return '';
                    }

                    $roles = implode(', ', $value);
                    if (strlen($value) > 60) {
                        $roles = substr($value, 60) . ' ...';
                    }
                    return $roles;
                }
            ])
            ->add('delete', TextColumn::class, [
                'label'   => 'Supprimer ?',
                'render'  => function($value, $role) {
                    return $this->renderView('admin/settings/roles/include/_delete-button.html.twig', [
                        'role' => $role,
                    ]);
                }
            ])
            ->addOrderBy('name')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Role::class
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/settings/roles/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route ("new", name="new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $this->createRoleName($role->getName());
            $role->setName($name);

            $this->entityManager->persist($role);
            $this->entityManager->flush();

            return $this->redirectToRoute('role_edit', [
                'id' => $role->getId()
            ]);
        }

        return $this->render('admin/settings/roles/new.html.twig', [
            'form'   => $form->createView(),
            'isEdit' => false
        ]);
    }

    /**
     * @Route ("edit/{id}", name="edit")
     *
     * @param Request $request
     * @param Role $role
     *
     * @return Response
     */
    public function edit(Request $request, Role $role): Response
    {
        $authorizations = $this->entityManager->getRepository(Authorization::class)
            ->findBy([], ['relatedEntity' => 'ASC']);

        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $this->createRoleName($role->getName());
            $role->setName($name);

            $this->entityManager->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('admin/settings/roles/edit.html.twig', [
            'form'              => $form->createView(),
            'authorizations'    => $authorizations,
            'role'              => $role,
            'isEdit'            => true
        ]);
    }

    /**
     * @Route ("add-authorization/{id}", name="add_authorization")
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function addAuthorization(Request $request, int $id): JsonResponse
    {
        $role = $this->entityManager->getRepository(Role::class)
            ->find($id);

        if (null === $role) {
            return new JsonResponse(
                'Une erreur est survenue. Contacter le support',
                400);
        }

        $this->roleTable->updateAuthorizationsInRole($request, $role);

        return new JsonResponse(
            'L\'autorisation a bien été modifiée',
            200
        );
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function delete(Role $role): JsonResponse
    {
        if (!$role instanceof Role) {
            return new JsonResponse('Role Not Found', 404);
        }

        $this->entityManager->remove($role);
        $this->entityManager->flush();

        return new JsonResponse('Role deleted with success', 200);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function createRoleName(string $name): string
    {
        $upperName   = strtoupper($name);
        $replaceName = str_replace(' ', '_', $upperName);

        if (0 === strpos($replaceName, 'ROLE_')) {
            return $replaceName;
        }

        return 'ROLE_' . $replaceName;
    }
}