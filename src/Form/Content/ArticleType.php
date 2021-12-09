<?php

namespace App\Form\Content;

use App\Entity\Contents\Article;
use App\Entity\Contents\ArticleCategory;
use App\Entity\Structure\Veterinary;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
            ->add('description', CKEditorType::class, [
                'label'       => 'Détail de l\'article',
                'required'    => true,
                'attr'        => [
                    'placeholder' => 'Détail de l\'article',
                ],
            ])
            ->add('isActivated', CheckboxType::class, [
                'label'    => 'Activer l\'article ?',
                'required' => false,
                'data'     => null !== $options['article'] ? $options['article']->getIsActivated() : true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label'       => 'Image correspondant à l\'article',
                'required'    => false,
                'attr'        => [
                    'lang'        => 'fr',
                    'placeholder' => 'Télécharger une image',
                ],
            ])
            ->add('articleCategory', Select2EntityType::class, [
                'label'         => 'Rattacher à une catégory',
                'required'      => true,
                'placeholder'   => 'Rattacher à une catégory',
                'class'         => ArticleCategory::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_article_category',
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
            'data_class' => Article::class,
            'article'    => null,
        ]);
    }
}