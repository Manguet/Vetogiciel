<?php


namespace App\Form\Structure;

use App\DBAL\Types\Structures\StructureTypeEnum;
use App\Entity\Structure\Clinic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClinicType extends AbstractType
{
    /**
     * @var StructureTypeEnum
     */
    private $structureTypes;

    /**
     * @param StructureTypeEnum $structureTypes
     */
    public function __construct(StructureTypeEnum $structureTypes)
    {
        $this->structureTypes = $structureTypes->getValues();
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
            ->add('name', TextType::class, [
                'label'    => 'Nom de la structure *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom de la structure (obligatoire)',
                ],
            ])
            ->add('fiscalDate', DateTimeType::class, [
                'label'       => 'Date fiscale',
                'required'    => false,
                'widget'      => 'single_text',
                'format'      => 'MM-dd',
                'html5'       => false,
                'attr'        => [
                    'class' => 'js-datepicker',
                ]
            ])
            ->add('status', TextType::class, [
                'label'    => 'Status de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Status de la structure',
                ]
            ])
            ->add('address', TextType::class, [
                'label'    => 'Adresse de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Adresse de la structure',
                ]
            ])
            ->add('address2', TextType::class, [
                'label'    => 'Complément d\'adresse',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Complément d\'adresse',
                ]
            ])
            ->add('postalCode', IntegerType::class, [
                'label'    => 'Code postal de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Code postal de la structure',
                ]
            ])
            ->add('city', TextType::class, [
                'label'    => 'Ville de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ville de la structure',
                ]
            ])
            ->add('phone', TextType::class, [
                'label'    => 'Téléphone de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Téléphone de la structure',
                ]
            ])
            ->add('phone2', TextType::class, [
                'label'    => 'Téléphone secondaire de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Téléphone secondaire de la structure',
                ]
            ])
            ->add('email', EmailType::class, [
                'label'    => 'E-mail de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'E-mail de la structure',
                ]
            ])
            ->add('siren', TextType::class, [
                'label'    => 'SIREN de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'SIREN de la structure',
                ]
            ])
            ->add('siret', TextType::class, [
                'label'    => 'SIRET de la structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'SIRET de la structure',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label'    => 'Type de structure',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Type de structure',
                ],
                'choices'  => $this->structureTypes,
                'multiple' => false,
                'expanded' => false,
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
            'data_class'     => Clinic::class,
        ]);
    }
}