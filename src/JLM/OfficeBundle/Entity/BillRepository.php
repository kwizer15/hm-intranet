<?php

namespace JLM\OfficeBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BillRepository extends EntityRepository
{
	public function getTotal()
	{
		$qb = $this->createQueryBuilder('q')
		->select('COUNT(q)');
		
		return (int) $qb->getQuery()
		->getSingleScalarResult();
	}
	
	public function getLastNumber()
	{
		$date = new \DateTime;
		$year = $date->format('Y');
		$qb = $this->createQueryBuilder('q')
			->select('SUBSTRING(q.number,5) as num')
			->where('SUBSTRING(q.creation, 1, 4) = :year')
			->orderBy('q.number','DESC')
			->setMaxResults(1)
			->setParameter('year',$year);
		$result = $qb->getQuery()->getResult();
//		var_dump($result); exit;
		if (!$result)
			return 0;
		else
			return $result[0]['num'];
	}
	
	public function search($query)
	{
		$qb = $this->createQueryBuilder('q')
				->where('q.number LIKE :query')
				->orWhere('q.trusteeName LIKE :query')
				->orWhere('q.trusteeAddress LIKE :query')
				->orderBy('q.creation','DESC')
				->setParameter('query', '%'.$query.'%');
		
		return $qb->getQuery()->getResult();
	}
	
	public function getCount($state = null)
	{
		$qb = $this->createQueryBuilder('t')
		->select('COUNT(t)');
		if ($state !== null)
			$qb->where('t.state = ?1')
			->setParameter(1,$state);
	
		return (int) $qb->getQuery()
		->getSingleScalarResult();
	}
	
	public function getByState($state = null,$limit = 10, $offset = 0)
	{
		$qb = $this->createQueryBuilder('t');
		if ($state)
		{
			$qb->where('t.state = ?1')
			->setParameter(1,$state)
			;
		}
		$qb->orderBy('t.creation','asc')
		->setFirstResult($offset)
		->setMaxResults($limit);
		return $qb->getQuery()->getResult();
	}
}