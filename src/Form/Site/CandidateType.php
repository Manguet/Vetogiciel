<?php

namespace App\Form\Site;

use App\DBAL\Types\User\GenderEnum;
use App\Entity\Contents\Candidate;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CandidateType extends AbstractType
{
    /**
     * @var GenderEnum
     */
    private $genderTypes;

    /**
     * @param GenderEnum $genderTypes
     */
    public function __construct(GenderEnum $genderTypes)
    {
        $this->genderTypes = $genderTypes->getValues();
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
            ->add('gender', ChoiceType::class, [
                'label'    => 'Civilité *',
                'required' => true,
                'attr'     => [
                    'placeholder' => '',
                ],
                'choices'  => $this->genderTypes,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('firstname', TextType::class, [
                'label'    => 'Prénom *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Prénom (obligatoire)',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label'    => 'Nom *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Nom (obligatoire)',
                ],
            ])
            ->add('email', TextType::class, [
                'label'    => 'Email *',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Adresse e-mail (obligatoire)',
                ]
            ])
            ->add('presentation', CKEditorType::class, [
                'label'       => 'Motivation *',
                'required'    => true,
                'attr'        => [
                    'placeholder' => 'Motivation (obligatoire)',
                ],
            ])
            ->add('cvFile', VichImageType::class, [
                'label'       => 'CV *',
                'required'    => true,
                'attr'        => [
                    'lang'        => 'fr',
                    'placeholder' => 'CV (formats acceptés : png, jpeg, jpg, pdf)',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Postuler',
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
            'data_class' => Candidate::class,
        ]);
    }
}