<?php

namespace App\Service\Datatable;

use App\Entity\Settings\Role;
use App\Entity\Structure\Veterinary;
use App\Interfaces\Datatable\DatatableFieldInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use LogicException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DatatableServices implements DatatableFieldInterface
{
    private Environment $templating;

    private EntityManagerInterface $entityManager;

    private Security $security;

    private string $template;

    private ?string $entityName;

    private ?string $authorizations;

    private ?string $url;

    private $role;

    /**
     * @param Environment $environment
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     */
    public function __construct(Environment $environment, EntityManagerInterface $entityManager, Security $security)
    {
        $this->templating    = $environment;
        $this->entityManager = $entityManager;
        $this->security      = $security;
    }

    /**
     * @param DataTable $table
     *
     * @return DatatableServices
     */
    public function addCreatedBy(DataTable $table): self
    {
        $table
            ->add('createdByVeterinary', TextColumn::class, [
                'label'     => 'Créé par',
                'orderable' => true,
                'render'    => function ($value, $content) {
                    $user = $content->getCreatedBy();

                    if (null === $user) {
                        return '';
                    }

                    $result = '';
                    if ($user instanceof Veterinary) {
                        $result = 'Dr. ';
                    }

                    $result .= $user->getFirstname() . ' ' . $user->getLastname();

                    return $result;
                }
            ]);

        return $this;
    }

    /**
     * @param DataTable $table
     * @param string $template
     * @param null|array $options
     *
     * @return DataTable
     */
    public function addDeleteField(DataTable $table, string $template, ?array $options = []): DataTable
    {
        $this->template = $template;

        $this->setOptions($options);

        $allAuthorizations = null;

        if (isset($options['authorizations']) && $user = $this->security->getUser()) {

            $roleUser = $user->getRoles();
            $this->role = $this->entityManager->getRepository(Role::class)
                ->findOneBy(['name' => $roleUser]);

            $allAuthorizations = $this->setAuthorization($options['authorizations']);
        }
        if (null === $allAuthorizations
                || in_array($allAuthorizations[0], $this->role->getAuthorizations(), true)
                || in_array($allAuthorizations[1], $this->role->getAuthorizations(), true)
                || in_array($allAuthorizations[2], $this->role->getAuthorizations(), true)
        ) {
            $table
                ->add('delete', TextColumn::class, [
                    'label'  => 'Supprimer ?',
                    'render' => function ($value, $entity)  {

                        if ($this->canAccessByLevel($this->role, $entity)) {
                            return $this->templating->render($this->template, [
                                $this->entityName => $entity,
                            ]);
                        }

                        return '';
                    }
                ]);
        }

        return $table;
    }

    /**
     * @param DataTable $table
     * @param string $fieldName
     * @param string $label
     * @param string $url
     * @param string|null $authorization
     *
     * @return DataTable
     */
    public function addFieldWithEditField(DataTable $table, string $fieldName, string $label,
                                          string $url, ?string $authorization = null): DataTable
    {
        $this->authorizations = $authorization;
        $this->url            = $url;

        $table
            ->add($fieldName, TextColumn::class, [
                'label'     => $label,
                'orderable' => true,
                'render'    => function ($value, $context) {
                    $role = $this->security->getUser()->getRoles();
                    $role = $this->entityManager->getRepository(Role::class)
                        ->findOneBy(['name' => $role]);

                    $allAuthorizations = $this->setAuthorization($this->authorizations);

                    if ($context instanceof UserInterface) {
                        $prefix = '';

                        if ($context instanceof Veterinary) {
                            $prefix = 'Dr. ';
                        }
                        $value = $prefix . $context->getFirstname() . ' ' . $context->getLastName();
                    }

                    if (empty($this->authorizations)
                        ||($role && $this->canAccessByLevel($role, $context) && (
                            in_array($allAuthorizations[0], $role->getAuthorizations(), true)
                            || in_array($allAuthorizations[1], $role->getAuthorizations(), true)
                            || in_array($allAuthorizations[2], $role->getAuthorizations(), true)
                            ))
                    )
                    {
                        return '<a href="/admin/' . $this->url . '/edit/' . $context->getId() . '">' . $value . '</a>';
                    }

                    return $value;
                }
            ]);

        return $table;
    }

    /**
     * @param array|null $options
     */
    private function setOptions(?array $options): void
    {
        if (!isset($options['entity'])) {
            throw new LogicException(
                'Merci d\'ajouter à minima l\'index : entity',
                400
            );
        }

        foreach ($options as $optionTitle => $optionValue) {

            switch ($optionTitle) {
                case 'entity';
                    $this->entityName = $optionValue;
                    break;

                case 'authorizations':
                    $this->authorizations = $optionValue;
                    break;

                default:
                    throw new InvalidArgumentException(
                        'L\'argument ' . $optionTitle . ' n\'est pas un argument valable'
                    );
            }
        }
    }

    /**
     * @param DataTable $table
     * @param $class
     */
    public function createDatatableAdapter(DataTable $table, $class): void
    {
        $table
            ->createAdapter(ORMAdapter::class, [
                'entity' => $class,
                'query'  => function (QueryBuilder $builder) use ($class) {

                    $user = $this->security->getUser();

                    if (null === $user) {
                        return '';
                    }

                    $role = $user->getRoles()[0];

                    $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);
                    $permissionLevel = $role->getPermissionLevel();

                    $qb = $builder
                        ->select('a')
                        ->from($class, 'a')
                    ;

                    if ($permissionLevel === 'society') {
                        $qb
                            ->leftJoin('a.createdByVeterinary', 'v')
                            ->where('v.clinic = :society')
                            ->orWhere('v.clinic is null');

                        if (method_exists($class, 'getCreatedByEmployee')) {
                            $qb
                                ->leftJoin('a.createdByEmployee', 'e')
                                ->orWhere('e.clinic = :society');
                        }

                        $qb
                            ->setParameter('society', $user->getClinic())
                        ;
                    }

                    if ($permissionLevel === 'user' && method_exists($class, 'getCreatedByEmployee')) {
                        $qb
                            ->where('a.createdByVeterinary = :user')
                            ->orWhere('a.createdByEmployee = :user')
                            ->setParameter('user', $user)
                        ;
                    }
                },
            ]);
    }

    /**
     * @param string|null $authorization
     *
     * @return array
     */
    private function setAuthorization(?string $authorization): array
    {
        if (!$authorization) {
            return [];
        }

        $authorizations = explode('_', $authorization);

        [$sector, $entity, $level] = $authorizations;

        return [
            $sector . '_FULL_ACCESS',
            $sector . '_' . $entity . '_MANAGE',
            $authorization
        ];
    }

    /**
     * @param Role|null $role
     * @param $entity
     *
     * @return bool
     */
    private function canAccessByLevel(?Role $role, $entity): bool
    {
        if (null === $role
            || $role->getPermissionLevel() === 'group'
            || !method_exists($entity, 'getCreatedBy')
            || null === $entity->getCreatedBy()
        )
        {
            return true;
        }

        $permissionLevel = $role->getPermissionLevel();
        $user            = $this->security->getUser();

        if ($permissionLevel === 'society'
            && $user
            && $user->getClinic() === $entity->getCreatedBy()->getClinic())
        {
            return true;
        }

        if ($permissionLevel === 'user'
            && $user
            && $user === $entity->getCreatedBy()
        ) {
            return true;
        }

        return false;
    }
}