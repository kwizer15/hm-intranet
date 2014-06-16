<?php

/*
 * This file is part of the intervention-bundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\InstallationBundle\Tests\Entity;

use JLM\InstallationBundle\Entity\PartCategory;
/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class PartCategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entity = new PartCategory('bar');
    }
    
    /** 
     * {@inheritdoc}
     */
    protected function assertPreConditions()
    {
        $this->assertSame('bar', $this->entity->getName());
    }

    public function testGetName()
    {
        $entity = new PartCategory('foo');
        $this->assertSame('foo', $entity->getName());
    }
}