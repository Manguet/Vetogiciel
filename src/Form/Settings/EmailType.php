<?php

namespace App\Form\Settings;

use App\Entity\Mail\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class EmailType extends AbstractType
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
                'label'    => 'Titre de l\'email',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Titre de l\'email',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Description',
                ]
            ])
            ->add('subject', TextType::class, [
                'label'    => 'Object de l\'email',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Objet de l\'email',
                ]
            ])
            ->add('isActivated', CheckboxType::class, [
                'label'    => 'Activé ce mail ?',
                'required' => false
            ])
            ->add('template', ChoiceType::class, [
                'label'    => 'Template',
                'required' => true,
                'choices'  => $options['templates']
            ])
            ->add('isExpeditorCurrentUser', CheckboxType::class, [
                'label'    => 'Utiliser l\'utilisateur en cours comme expéditeur ?',
                'required' => false
            ])
            ->add('expeditor', TextType::class, [
                'label'    => 'Expéditeur',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Expéditeur',
                ],
            ])
            ->add('isDestinatorCurrentUser', CheckboxType::class, [
                'label'    => 'Utiliser l\'utilisateur en cours comme destinataire ?',
                'required' => false
            ])

            ->add('addDestinator', TextType::class, [
                'label'    => 'Ajouter un destinataire',
                'required' => false,
                'mapped'   => false,
                'attr'     => [
                    'placeholder' => 'Ajouter un destinaitaire'
                ]
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
            'data_class' => Email::class,
            'templates'  => null,
        ]);
    }
}