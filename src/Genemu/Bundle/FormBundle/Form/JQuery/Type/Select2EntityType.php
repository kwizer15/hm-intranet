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

use Genemu\Bundle\FormBundle\Form\JQuery\DataTransformer\ArrayToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Select2Type to JQueryLib
 *
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 * @author Chris Tickner <chris.tickner@gmail.com>
 */
class Select2EntityType extends AbstractType
{
    private $configs;

    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configs'] = $options['configs'];

        // Adds a custom block prefix
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes'], true),
            0,
            'genemu_jqueryselect2'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaults = $this->configs;
        $resolver
            ->setDefaults(array(
                'configs'       => $defaults,
                'transformer'   => null,
            ))
            ->setNormalizer('configs', function (Options $options, $configs) use ($defaults) {
                    return array_merge($defaults, $configs);
                }
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryselect2_entity';
    }
}
