<?php

namespace App\Form\Content;

use App\Entity\Contents\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ArticleType extends AbstractType
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
                'label'    => 'Titre de l\'article *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Titre de l\'article (obligatoire)',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'Détail de l\'article',
                'required'    => true,
                'attr'        => [
                    'placeholder' => 'Détail de l\'article',
                ]
            ])
            ->add('isActivated', CheckboxType::class, [
                'label'    => 'Activer l\'article ?',
                'required' => false,
                'data'     => null !== $options['article'] ? $options['article']->getIsActivated() : true,
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
            'data_class' => Article::class,
            'article'    => null,
        ]);
    }
}