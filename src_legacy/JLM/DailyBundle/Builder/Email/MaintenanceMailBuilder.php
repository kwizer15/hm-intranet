<?php

/*
 * This file is part of the JLMDailyBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\DailyBundle\Builder\Email;

use JLM\CoreBundle\Builder\MailBuilderAbstract;
use JLM\DailyBundle\Model\InterventionInterface;
use JLM\DailyBundle\Model\MaintenanceInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
abstract class MaintenanceMailBuilder extends InterventionMailBuilder
{
    public function __construct(MaintenanceInterface $maintenance)
    {
        parent::__construct($maintenance);
    }
    
    public function getMaintenance()
    {
        return $this->getIntervention();
    }
}
