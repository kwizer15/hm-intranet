<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Entity;

use JLM\ProductBundle\Model\ProductInterface;
use JLM\CommerceBundle\Model\CommercialPartLineInterface;
use JLM\CommerceBundle\Model\DocumentLineInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
abstract class CommercialPartLine extends DocumentLine implements CommercialPartLineInterface
{

}