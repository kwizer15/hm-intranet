<?php

/*
 * This file is part of the JLMBillBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\BillBundle\Builder;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
interface BillLineBuilderInterface
{
    public function create();
    
    public function buildReference();
    
    public function buildProduct();
    
    public function buildQuantity();
    
    public function buildPrice();
    
    public function getLine();
}