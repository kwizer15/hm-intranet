<?php

/*
 * This file is part of the installation-bundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\InstallationBundle\Entity;

use JLM\InstallationBundle\Model\BayInterface;
use JLM\InstallationBundle\Model\PartInterface;
use JLM\CollectiveHousingBundle\Model\PropertyInterface;
/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class Bay implements BayInterface
{
    /**
     * @var PartInterface
     */
    private $part;
    
    /**
     * @var PropertyInterface
     */
    private $property;
    
    /**
     * 
     * @param PropertyInterface $property
     * @param PartInterface $part
     */
    public function __construct(PropertyInterface $property, PartInterface $part = null)
    {
        $this->setProperty($property);
        $this->setPart($part);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPart()
    {
        return $this->part;
    } 
    
    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->property;
    }
    
    /**
     * 
     * @param PartInterface $part
     * @return \JLM\InstallationBundle\Entity\Bay
     */
    public function setPart(PartInterface $part = null)
    {
        $this->part = $part;
        
        return $this;
    }
    
    /**
     * 
     * @param PropertyInterface $property
     * @return \JLM\InstallationBundle\Entity\Bay
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->property = $property;
        
        return $this;
    }
}