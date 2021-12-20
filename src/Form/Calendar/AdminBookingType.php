<?php

namespace App\Form\Calendar;

use App\Entity\Calendar\Booking;
use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminBookingType extends AbstractType
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
                'label'    => 'Motif',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Motif',
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label'    => 'Choisir une couleur',
                'required' => true,
                'choices'  => [
                    'Rouge'  => '#E74C3C',
                    'Violet' => '#9B59B6',
                    'Bleu'   => '#5DADE2',
                    'Vert'   => '#58D68D',
                    'Jaune'  => '#F4D03F',
                    'Orange' => '#F5B041',
                    'Gris'   => '#CACFD2',
                ],
                'data'     => $options['booking']->getColor() ?? '#E74C3C',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('client', Select2EntityType::class, [
                'label'         => 'Pour quel client ?',
                'required'      => false,
                'placeholder'   => 'Pour quel client ?',
                'class'         => Client::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_client',
                'remote_params' => [
                    'veterinary' => $options['veterinary']->getId()
                ],
                'language'      => 'fr',
                'scroll'        => true,
                'allow_clear'   => true,
                'text_property' => 'lastname',
                'allow_add'     => [
                    'enabled'        => true,
                    'new_tag_text'   => ' <i>(...créer...)</i>',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", " "]'
                ],
            ])
            ->add('animal', Select2EntityType::class, [
                'label'         => 'Pour quel animal ?',
                'required'      => false,
                'placeholder'   => 'Pour quel animal ?',
                'class'         => Animal::class,
                'multiple'      => false,
                'remote_route'  => 'admin_ajax_animal',
                'language'      => 'fr',
                'scroll'        => true,
                'req_params' => ['client' => 'parent.children[client]'],
                'allow_clear'   => true,
                'text_property' => 'name',
                'allow_add'     => [
                    'enabled'        => true,
                    'new_tag_text'   => ' <i>(...créer...)</i>',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", " "]'
                ],
            ])
            ->add('beginAt', TextType::class, [
                'label'       => 'Commence à',
                'required'    => true,
                'mapped'      => false,
                'attr'        => [
                    'placeholder' => 'Définir une heure de début (format jour/mois/année heure:minutes)',
                ],
                'data' => $options['booking']->getBeginAt() ? $options['booking']->getBeginAt()->format('d/m/Y H:i') : null
            ])
            ->add('endAt', TextType::class, [
                'label'    => 'Fini à',
                'required' => true,
                'mapped'   => false,
                'attr'     => [
                    'placeholder' => 'Définir une heure de début (format jour/mois/année heure:minutes)',
                ],
                'data' => $options['booking']->getEndAt() ? $options['booking']->getEndAt()->format('d/m/Y H:i') : null

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
            'data_class' => Booking::class,
            'booking'    => null,
            'veterinary' => null,
        ]);
    }
}