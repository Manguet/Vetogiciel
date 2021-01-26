<?php

namespace App\Security\Form;

use App\Entity\Structure\Clinic;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use App\Form\DataTransformer\SectorPropertyToTransform;
use App\Form\DataTransformer\StructurePropertyToTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Registration from type
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RegistrationFormType extends AbstractType
{
    /**
     * @var StructurePropertyToTransform
     */
    private $structurePropertyToTransform;

    /**
     * @var SectorPropertyToTransform
     */
    private $sectorPropertyToTransform;

    /**
     * @param StructurePropertyToTransform $structurePropertyToTransform
     * @param SectorPropertyToTransform $sectorPropertyToTransform
     */
    public function __construct(StructurePropertyToTransform $structurePropertyToTransform, SectorPropertyToTransform $sectorPropertyToTransform)
    {
        $this->structurePropertyToTransform = $structurePropertyToTransform;
        $this->sectorPropertyToTransform    = $sectorPropertyToTransform;
    }

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
                'label'    => 'Email',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Email',
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped'      => false,
                'label'       => 'J\'accepte les conditions d\'utilisations',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped'          => false,
                'required'        => true,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'options'         => [
                    'attr' => [
                        'class' => 'password-field'
                    ],
                ],
                'constraints'     => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre mot de passe.',
                    ]),
                    new Length([
                        'min'        => 5,
                        'minMessage' => 'Votre mot de passe doit avoir un minimum de  {{ limit }} caractères',
                        'max'        => 4096,
                    ]),
                ],
                'first_options'  => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
            ])

            ->add('firstname', TextType::class, [
                'label'    => 'Prénom',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Prénom',
                ]
            ])

            ->add('lastname', TextType::class, [
                'label'    => 'Nom',
                'required' => true,
                'constraints'     => [
                    new NotBlank([
                        'message' => 'Merci de saisir un nom.',
                    ]),
                    new Length([
                        'min'        => 1,
                        'minMessage' => 'Le nom doit avoir un minimum de {{ limit }} caractère',
                        'max'        => 4096,
                    ]),
                ],
                'attr'     => [
                    'placeholder' => 'Nom de famille',
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

            ->add('color', TextType::class, [
                'label'    => 'Choisir une couleur',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Couleur au type #AED6F1',
                ]
            ])

            ->add('number', TextType::class, [
                'label'    => 'Enregistrer son numéro d\'ordre vétérinaire',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Numéro d\'enregistrement à l\'ordre vétérinaire',
                ]
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

        $builder->get('clinic')
            ->addModelTransformer($this->structurePropertyToTransform);

        $builder->get('sector')
            ->addModelTransformer($this->sectorPropertyToTransform);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Veterinary::class,
        ]);
    }
}
