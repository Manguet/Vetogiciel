<?php

namespace App\Form\Content;

use App\Entity\Contents\Commentary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CommentType extends AbstractType
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
            ->add('description', TextareaType::class, [
                'label'    => 'Tapez votre commentaire',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Tapez votre commentaire'
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
            'data_class' => Commentary::class,
        ]);
    }
}