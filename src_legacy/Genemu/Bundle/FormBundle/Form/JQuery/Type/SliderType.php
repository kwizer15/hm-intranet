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

use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SliderType
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class SliderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $options;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'orientation' => 'horizontal'
        ));

        $resolver->setAllowedValues('orientation', array(
                'horizontal',
                'vertical'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryslider';
    }
}
