<?php

/*
 * This file is part of the JLMFollowBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\FollowBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JLM\DailyBundle\JLMDailyEvents;
use JLM\DailyBundle\Builder\InterventionWorkBuilder;
use JLM\DailyBundle\Event\InterventionEvent;
use JLM\DailyBundle\Factory\WorkFactory;
use JLM\FollowBundle\Entity\StarterIntervention;
use JLM\FollowBundle\Entity\Thread;
use JLM\FollowBundle\Factory\ThreadFactory;
use JLM\DailyBundle\Entity\Intervention;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class InterventionSubscriber implements EventSubscriberInterface
{
   
    /**
     * @var ObjectManager
     */
    private $om;
    
    /**
     * Constructor
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
                JLMDailyEvents::INTERVENTION_SCHEDULEWORK   => 'createThread',
                JLMDailyEvents::INTERVENTION_UNSCHEDULEWORK => 'deleteThread',
               ];
    }
    
    /**
     * Create thread since intervention
     * @param InterventionEvent $event
     */
    public function createThread(InterventionEvent $event)
    {
        $entity = $event->getIntervention();
        $thread = ThreadFactory::create($entity);
        $this->om->persist($thread);
        $this->om->flush();
    }
    
    /**
     * Create thread since intervention
     * @param InterventionEvent $event
     */
    public function deleteThread(InterventionEvent $event)
    {
        $entity = $event->getIntervention();
        try {
            $starter= $this->om
                ->getRepository('JLMFollowBundle:StarterIntervention')
                ->findOneBy(['intervention' => $entity])
            ;
            $thread = $this->om->getRepository('JLMFollowBundle:Thread')->findOneBy(['starter' => $starter]);
            $this->om->remove($thread);
            $this->om->flush();
        } catch (\Exception $e) {
        }
    }
}
