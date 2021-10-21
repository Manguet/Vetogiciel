<?php

namespace App\Form\Structure;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SectorFormType extends AbstractType
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
                'label'    => 'Nom du secteur',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom du secteur',
                ],
            ])
            ->add('description', CKEditorType::class, [
                'label'       => 'Description du secteur',
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'Description du secteur',
                ],
            ])
            ->add('icon', TextType::class, [
                'label'    => 'Icone',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Choisir sur https://fontawesome.com',
                ],
            ])
            ->add('veterinaries', Select2EntityType::class, [
                'label'         => 'Vétérinaires dans ce secteur',
                'required'      => false,
                'placeholder'   => 'Vétérinaires dans ce secteur',
                'class'         => Veterinary::class,
                'multiple'      => true,
                'remote_route'  => 'admin_ajax_veterinary',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'lastname',
                'attr' => [
                    'style' => 'width: 100%'
                ],
            ])
            ->add('employees', Select2EntityType::class, [
                'label'         => 'Employés dans ce secteur',
                'required'      => false,
                'placeholder'   => 'Employés dans ce secteur',
                'class'         => Employee::class,
                'multiple'      => true,
                'remote_route'  => 'admin_ajax_employee',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'firstname',
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
            'data_class'     => Sector::class,
        ]);
    }
}