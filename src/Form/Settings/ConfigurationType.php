<?php

namespace App\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['configurations'] as $configuration) {

            if ($configuration->getFieldType() === CheckboxType::class) {
                $datas = isset($configuration->getDatas()['values']) && $configuration->getDatas()['values'];
            } else {
                $datas = $configuration->getDatas()['values'] ?? null;
            }

            $builder
                ->add($configuration->getName(), $configuration->getFieldType(), [
                    'label'    => $configuration->getSettings()['label'] ?? '',
                    'required' => $configuration->getSettings()['required'] ?? false,
                    'attr'     => [
                        'placeholder' => $configuration->getSettings()['placeholder'] ?? '',
                    ],
                    'data'     => $datas ?? null
                ]);
        }

        $builder
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'custom-button',
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'     => null,
            'configurations' => null
        ]);
    }
}