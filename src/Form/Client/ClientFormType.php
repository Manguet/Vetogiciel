<?php

namespace App\Form\Client;

use App\Entity\Patients\Client;
use App\Traits\Role\AddRoleTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClientFormType extends AbstractType
{
    use AddRoleTrait;

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
            ->add('address', TextareaType::class, [
                'label'    => 'Adresse',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Adresse',
                ],
            ])
            ->add('address2', TextareaType::class, [
                'label'    => 'Complément d\'adresse',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Complément d\'adresse',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label'    => 'Code postal',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Code postal',
                ],
            ])
            ->add('city', TextType::class, [
                'label'    => 'Ville',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ville',
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'Téléphone fixe',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Téléphone fixe',
                ],
            ])
            ->add('phoneNumber2', TextType::class, [
                'label'    => 'Téléphone Secondaire',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Téléphone secondaire',
                ],
            ])
//            ->add('comment')
//            ->add('animals')
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
            'data_class'     => Client::class,
            'enablePassword' => null,
        ]);
    }
}