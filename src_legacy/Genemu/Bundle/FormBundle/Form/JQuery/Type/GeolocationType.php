<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\JQuery\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Genemu\Bundle\FormBundle\Form\Core\EventListener\GeolocationListener;

/**
 * GeolocationType to JQueryLib
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class GeolocationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('address', TextType::class);

        foreach (array('latitude', 'longitude', 'locality', 'country') as $field) {
            $option = $options[$field];

            if (isset($option['enabled']) && !empty($option['enabled'])) {
                $type = 'text';
                if (isset($option['hidden']) && !empty($option['hidden'])) {
                    $type = 'hidden';
                }

                $builder->add($field, $type);
            }
        }

        $builder
            ->addEventSubscriber(new GeolocationListener());
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'configs'   => [],
            'elements'  => [],
            'map' => $options['map']
        ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'map' => false,
            'latitude' => [
                'enabled' => false,
                'hidden' => false,
            ],
            'longitude' => [
                'enabled' => false,
                'hidden' => false,
            ],
            'locality' => [
                'enabled' => false,
                'hidden' => false,
            ],
            'country' => [
                'enabled' => false,
                'hidden' => false,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'genemu_jquerygeolocation';
    }
}
