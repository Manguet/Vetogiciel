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
use Twig\Environment;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DatatableServices implements DatatableFieldInterface
{
    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $template;

    /**
     * @var null|string
     */
    private $entityName;

    /**
     * @var null|string
     */
    private $authorizations;

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

        $table
            ->add('delete', TextColumn::class, [
                'label'  => 'Supprimer ?',
                'render' => function ($value, $entity) {
                    return $this->templating->render($this->template, [
                        $this->entityName => $entity,
                    ]);
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
                            ->orWhere('v.clinic is null')
                            ->leftJoin('a.createdByEmployee', 'e')
                            ->orWhere('e.clinic = :society')
                            ->setParameter('society', $user->getClinic())
                        ;
                    }

                    if ($permissionLevel === 'user') {
                        $qb
                            ->where('a.createdByVeterinary = :user')
                            ->orWhere('a.createdByEmployee = :user')
                            ->orWhere('a.createdByEmployee is null')
                            ->setParameter('user', $user)
                        ;
                    }
                },
            ]);
    }
}