<?php

/*
 * This file is part of the  package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ModelBundle\Entity;

use JLM\ProductBundle\Entity\Product;
/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class Product extends Product
{
    public function __construct()
    {
        parent::__construct();
        echo "JLMModelBundle:Product DEPRECATED";
    }
}