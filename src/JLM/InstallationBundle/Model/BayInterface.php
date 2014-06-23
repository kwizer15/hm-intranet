<?php

/*
 * This file is part of the installation-bundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\InstallationBundle\Model;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
interface BayInterface
{
    /**
     * @return PartInterface
     */
    public function getPart();
    
    /**
     * @return JLM\CollectiveHousing\Model\PropertyInterface
     */
    public function getProperty();
}