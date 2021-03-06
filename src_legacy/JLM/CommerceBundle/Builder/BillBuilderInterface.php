<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Builder;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
interface BillBuilderInterface
{
    /**
     * @return BillInterface
     */
    public function getBill();

    public function create();
    
    public function buildCreation();
    
    public function buildLines();
    
    public function buildCustomer();
    
    public function buildBusiness();

    public function buildReference();
    
    public function buildIntro();
    
    public function buildDetails();
    
    public function buildConditions();
}
