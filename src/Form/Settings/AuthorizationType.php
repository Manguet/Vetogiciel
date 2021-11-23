<?php

namespace App\Form\Settings;

use App\Entity\Settings\Authorization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AuthorizationType extends AbstractType
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
            ->add('relatedEntity', ChoiceType::class, [
                'label'    => 'Entité relative à l\'autorisation',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Entité relative à l\'autorisation',
                ],
                'choices'  => $options['entities']
            ])
            ->add('canAccess', TextType::class, [
                'label'    => 'Autorisation d\'accès',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Autorisation d\'accès',
                ],
            ])
            ->add('canAdd', TextType::class, [
                'label'    => 'Autorisation d\'ajout',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Autorisation d\'ajout',
                ],
            ])
            ->add('canShow', TextType::class, [
                'label'    => 'Autorisation de visionnage',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Autorisation de visionnage',
                ],
            ])
            ->add('canEdit', TextType::class, [
                'label'    => 'Autorisation d\'édition',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Autorisation d\'édition',
                ],
            ])
            ->add('canDelete', TextType::class, [
                'label'    => 'Autorisation de suppression',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Autorisation de suppression',
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
            'data_class' => Authorization::class,
            'entities'   => null
        ]);
    }
}