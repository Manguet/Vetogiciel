<?php

namespace App\Form\Content;

use App\Entity\Contents\JobOffer;
use App\Entity\Contents\JobOfferType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobType extends AbstractType
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
                'label'    => 'Titre de l\'offre *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Titre de l\'offre (obligatoire)',
                ],
            ])
            ->add('description', CKEditorType::class, [
                'label'       => 'Détail de l\'offre',
                'required'    => true,
                'attr'        => [
                    'placeholder' => 'Détail de l\'offre',
                ],
            ])
            ->add('isActivated', CheckboxType::class, [
                'label'    => 'Activer l\'offre ?',
                'required' => false,
                'data'     => null !== $options['joboffer'] ? $options['joboffer']->getIsActivated() : true,
            ])
            ->add('type', Select2EntityType::class, [
                'label'         => 'Rattacher à une catégorie',
                'required'      => true,
                'placeholder'   => 'Rattacher à une catégorie',
                'class'         => JobOfferType::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_joboffer_category',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'title',
                'attr' => [
                    'style' => 'width: 100%'
                ],
                'allow_add'     => [
                    'enabled'        => true,
                    'new_tag_text'   => ' <i>(...créer...)</i>',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", " "]'
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
            'data_class' => JobOffer::class,
            'joboffer'   => null,
        ]);
    }
}