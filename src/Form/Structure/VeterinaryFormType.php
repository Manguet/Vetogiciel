<?php

namespace App\Form\Structure;

use App\Entity\Structure\Clinic;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VeterinaryFormType extends AbstractType
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
            ->add('email', TextType::class, [
                'label'    => 'Email *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Adresse e-mail (obligatoire)',
                ]
            ]);

        if ($options['enablePassword']) {
            $builder
                ->add('password', RepeatedType::class, [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passes doivent être identiques',
                    'required'        => true,
                    'first_options'   => [
                        'label'       => 'Mot de passe *',
                        'attr'        => [
                            'placeholder' => 'Mot de passe (obligatoire)',
                        ],
                    ],
                    'second_options'  => [
                        'label'       => 'Confirmer le mot de passe *',
                        'attr'        => [
                            'placeholder' => 'Confirmer le mot de passe (obligatoire)',
                        ],
                    ],
                ]);
        }

        $builder
            ->add('firstname', TextType::class, [
                'label'    => 'Prénom',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Prénom',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label'    => 'Nom *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom (obligatoire)',
                ],
            ])
            ->add('color', ColorType::class, [
                'label'    => 'Couleur',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Selectionner une couleur',
                ]
            ])
            ->add('number', TextType::class, [
                'label'    => 'Numéro d\'ordre',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Numéro d\'ordre vétérinaire',
                ]
            ])
            ->add('clinic', Select2EntityType::class, [
                'label'         => 'Structure de rattachement',
                'required'      => false,
                'placeholder'   => 'Structure de rattachement',
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
            ->add('sector', Select2EntityType::class, [
                'label'         => 'Secteur d\'activité',
                'required'      => false,
                'placeholder'   => 'Secteur d\'activité',
                'class'         => Sector::class,
                'multiple'      => true,
                'remote_route'  => 'admin_ajax_sector',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'name',
                'attr' => [
                    'style' => 'width: 100%'
                ],
            ])

        ;

        if ($options['isShow']) {

            $builder
                ->add('isVerified', CheckboxType::class, [
                    'label'    => 'Email vérifié ?',
                    'required' => false,
                ])
            ;
        }

        $builder
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
            'data_class'     => Veterinary::class,
            'enablePassword' => null,
            'isShow'         => null,
        ]);
    }
}