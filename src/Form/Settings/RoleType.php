<?php

namespace App\Form\Settings;

use App\Entity\Settings\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RoleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'Nom du rôle',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom du rôle'
                ]
            ])
            ->add('permissionLevel', ChoiceType::class, [
                'label'       => 'Niveau de permission',
                'required'    => false,
                'placeholder' => 'Sélectionner un niveau de permission',
                'choices'     => [
                    'Utilisateur' => 'user',
                    'Clinique'    => 'society',
                    'Groupement'  => 'group'
                ]
            ])
            ->add('parentRole', Select2EntityType::class, [
                'label'         => 'Rôle parent',
                'required'      => false,
                'placeholder'   => 'Rôle parent',
                'class'         => Role::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_role',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'name',
                'attr' => [
                    'style' => 'width: 100%'
                ],
            ])
            ->add('childRoles',Select2EntityType::class, [
                'label'         => 'Rôles enfants',
                'required'      => false,
                'placeholder'   => 'Rôles enfants',
                'class'         => Role::class,
                'multiple'      => true,
                'remote_route'  => 'admin_ajax_role',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'name',
                'attr' => [
                    'style' => 'width: 100%'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr'  => [
                    'class' => 'custom-button',
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Role::class,
            'allow_extra_fields' => true,
        ]);
    }
}