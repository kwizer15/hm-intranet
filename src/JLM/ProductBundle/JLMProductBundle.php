<?php

namespace JLM\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JLM\ProductBundle\DependencyInjection\Compiler\FormPass;

class JLMProductBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FormPass());
    }
}
