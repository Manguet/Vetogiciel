<?php

namespace App\Form\Structure;

use App\Entity\Structure\Clinic;
use App\Entity\Structure\Prestation;
use App\Entity\Structure\Vat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class PrestationType extends AbstractType
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
            ->add('title', TextType::class, [
                'label'    => 'Nom de la prestation *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom de la prestation (obligatoire)',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description de la prestation',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Description de la prestation',
                ],
            ])
            ->add('code', TextType::class, [
                'label'    => 'Code Prestation *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Code unique de la prestation (obligatoire)',
                ]
            ])
            ->add('PriceHT', TextType::class, [
                'label'    => 'Prix HT *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Prix Hors Taxes (obligatoire)',
                ]
            ])
            ->add('vat', Select2EntityType::class, [
                'label'         => 'Sélectionner une TVA *',
                'required'      => true,
                'placeholder'   => 'Sélectionner une TVA (obligatoire)',
                'class'         => Vat::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_vat',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'title',
                'attr' => [
                    'style' => 'width: 100%'
                ],
            ])
            ->add('clinic', Select2EntityType::class, [
                'label'         => 'Rattacher à une clinique',
                'required'      => true,
                'placeholder'   => 'Rattacher à une clinique',
                'class'         => Clinic::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_structure',
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}