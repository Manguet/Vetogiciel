<?php

namespace App\Form\Structure;

use App\DBAL\Types\Structures\VeterinaryEnum;
use App\Entity\Settings\Role;
use App\Entity\Structure\Clinic;
use App\Entity\Structure\Sector;
use App\Entity\Structure\Veterinary;
use App\Traits\Role\AddRoleTrait;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VeterinaryFormType extends AbstractType
{
    use AddRoleTrait;

    /**
     * @var VeterinaryEnum
     */
    private $veterinaryTypes;

    /**
     * @param VeterinaryEnum $veterinaryTypes
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(VeterinaryEnum $veterinaryTypes, EntityManagerInterface $entityManager)
    {
        $this->veterinaryTypes = $veterinaryTypes->getValues();

        $roles = $entityManager->getRepository(Role::class)
            ->findBy([], ['name' => 'ASC']);

        foreach ($roles as $role) {
            $this->roles[$role->getName()] = $role->getName();
        }
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
            ->add('photoFile', VichImageType::class, [
                'label'       => 'Photo de profil',
                'required'    => false,
                'attr'        => [
                    'lang'        => 'fr',
                    'placeholder' => 'Télécharger une photo de profil',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label'    => 'Type de vétérinaire',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Type de vétérinaire',
                ],
                'choices'  => $this->veterinaryTypes,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('presentation', CKEditorType::class, [
                'label'       => 'Présentation générale',
                'required'    => true,
                'attr'        => [
                    'placeholder' => 'Présentation générale',
                ],
            ])
            ->add('cvFile', VichImageType::class, [
                'label'       => 'CV du vétérinaire',
                'required'    => false,
                'attr'        => [
                    'lang'        => 'fr',
                    'placeholder' => 'CV du vétérinaire (format : png, jpeg, jpg, pdf)',
                ],
            ])
            ->add('facebook', TextType::class, [
                'label'      => 'Lien vers le Facebook',
                'required'   => false,
                'attr'       => [
                    'placeholder' => 'Lien vers le Facebook'
                ]
            ])
            ->add('instagram', TextType::class, [
                'label'      => 'Lien vers le Instagram',
                'required'   => false,
                'attr'       => [
                    'placeholder' => 'Lien vers le Instagram'
                ]
            ])
            ->add('linkedin', TextType::class, [
                'label'      => 'Lien vers le Linkedin',
                'required'   => false,
                'attr'       => [
                    'placeholder' => 'Lien vers le Linkedin'
                ]
            ])
            ->add('twitter', TextType::class, [
                'label'      => 'Lien vers le Twitter',
                'required'   => false,
                'attr'       => [
                    'placeholder' => 'Lien vers le Twitter'
                ]
            ])
            ->add('youtube', TextType::class, [
                'label'      => 'Lien vers le Youtube',
                'required'   => false,
                'attr'       => [
                    'placeholder' => 'Lien vers le Youtube'
                ]
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

        $this->addRoleField($builder, $options);

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