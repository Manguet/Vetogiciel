<?php


namespace App\Form\Animal;

use App\Entity\Patients\Animal;
use App\Entity\Patients\Race;
use App\Entity\Patients\Species;
use App\Form\DataTransformer\RacePropertyToTransform;
use App\Form\DataTransformer\SpeciesPropertyTranform;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AnimalType extends AbstractType
{
    private RacePropertyToTransform $racePropertyTransform;

    private SpeciesPropertyTranform $speciesPropertyTransform;


    /**
     * AnimalType constructor.
     *
     * @param RacePropertyToTransform $racePropertyTransform
     * @param SpeciesPropertyTranform $speciesPropertyTranform
     */
    public function __construct(RacePropertyToTransform $racePropertyTransform,
                                SpeciesPropertyTranform $speciesPropertyTranform)
    {
        $this->racePropertyTransform    = $racePropertyTransform;
        $this->speciesPropertyTransform = $speciesPropertyTranform;
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
                'label'    => 'Nom *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom de l\'animal (obligatoire)',
                ]
            ])
            ->add('birthdate', DateTimeType::class, [
                'label'       => 'Date de naissance (si connue)',
                'required'    => false,
                'widget'      => 'single_text',
                'format'      => 'yyyy-MM-dd',
                'html5'       => false,
                'attr'        => [
                    'class' => 'js-datepicker',
                ]
            ])
            ->add('age', IntegerType::class, [
                'label'    => 'Age (si date de naissance inconnue)',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Age de l\'animal',
                ],
            ])
            ->add('color', TextType::class, [
                'label'    => 'Couleur',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Couleur du pelage',
                ],
            ])
            ->add('transponder', TextType::class, [
                'label'    => 'Transpondeur',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Transpondeur de l\'animal',
                ],
            ])
            ->add('tatoo', TextType::class, [
                'label'    => 'Tatouage',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Tatouage de l\'animal',
                ],
            ])
            ->add('idLocalization', ChoiceType::class, [
                'label'    => 'Localisation de l\'identification',
                'required' => false,
                'choices'  => [
                    'GJG' => 'Gouttière Jugulaire Gauche',
                    'OG'  => 'Oreille Gauche',
                ]
            ])
            ->add('isLof', CheckboxType::class, [
                'label'    => 'LOF / LOOF ?',
                'required' => false,
            ])
            ->add('isInsured', CheckboxType::class, [
                'label'    => 'Assuré ?',
                'required' => false,
            ])
            ->add('race', Select2EntityType::class, [
                'label'         => 'Race de l\'animal',
                'required'      => false,
                'placeholder'   => 'Race de l\'animal',
                'class'         => Race::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_race',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'name',
                'allow_add'     => [
                    'enabled'        => true,
                    'new_tag_text'   => ' <i>(...créer...)</i>',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", " "]'
                ],
            ])
            ->add('species', Select2EntityType::class, [
                'label'         => 'Espèce de l\'animal',
                'required'      => false,
                'placeholder'   => 'Espèce de l\'animal',
                'class'         => Species::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_species',
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'name',
                'allow_add'     => [
                    'enabled'        => true,
                    'nex_tag_text'   =>' <i>(...créer...)</i>',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", " "]'
                ],
                'req_params' => ['race' => 'parent.children[race]'],
                'callback'    => function (QueryBuilder $qb, $data) {
                    $qb->andWhere('e.race = :race');

                    if ($data instanceof Request) {
                        $qb->setParameter('race', $data->get('race'));
                    } else {
                        $qb->setParameter('race', $data['race']);
                    }

                },
            ])
//            ->add('comment')

            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr'  => [
                    'class' => 'custom-button',
                ],
            ])
        ;

        if ($options['isNew']) {

            $builder->get('race')
                ->addModelTransformer($this->racePropertyTransform);

            $builder->get('species')
                ->addModelTransformer($this->speciesPropertyTransform);

        }
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
            'isNew'      => null,
        ]);
    }
}