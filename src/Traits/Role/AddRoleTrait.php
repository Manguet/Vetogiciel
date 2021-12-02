<?php

namespace App\Traits\Role;

use App\Entity\Settings\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait AddRoleTrait
{
    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $roles = $entityManager->getRepository(Role::class)
            ->findBy([], ['name' => 'ASC']);

        foreach ($roles as $role) {
            $this->roles[$role->getName()] = $role->getName();
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return FormBuilderInterface
     */
    public function addRoleField(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        return $builder
            ->add('roles', ChoiceType::class, [
                'label'         => 'Rôle',
                'required'      => false,
                'placeholder'   => '--- Sélectionnez un rôle ---',
                'choices'       => $this->roles,
                'mapped'        => false,
                'data'          => $options['data'] ? $options['data']->getRoles()[0] : null
            ])
        ;
    }
}