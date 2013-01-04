<?php

namespace JLM\DailyBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * InterventionRepository
 */
class InterventionRepository extends EntityRepository
{
	public function getPrioritary($limit = null, $offset = null)
	{
		$qb = $this->createQueryBuilder('i')
			->where('i.officeAction IS NULL')
			->orderBy('i.priority','desc')
			->orderBy('i.creation','desc');
		if ($offset)
			$qb->setFirstResult( $offset );
		if ($limit)
   			$qb->setMaxResults( $limit );
		return $qb->getQuery()->getResult();
	}
	
	public function getCountOpened()
	{
		$qb = $this->createQueryBuilder('i')
			->select('COUNT(i)')
			->where('i.officeAction IS NULL');
		return (int) $qb->getQuery()
			->getSingleScalarResult();
	}
	
	public function getWithDate(\DateTime $date1, \DateTime $date2 = null)
	{
		if (empty($date2))
		{
			$date2 = \DateTime::createFromFormat('Ymd',$date1->format('Ymd'));
		}
		$date2->add(new \DateInterval('P1D'));
		$q = $this->getEntityManager()->createQuery('SELECT i FROM JLM\DailyBundle\Entity\Shifting i JOIN i.shiftTechnicians s WHERE s.begin BETWEEN ?1 AND ?2');
		$q->setParameter(1,$date1);
		$q->setParameter(2,$date2);
		return $q->getResult();
	}
}