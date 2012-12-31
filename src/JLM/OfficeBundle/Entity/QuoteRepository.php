<?php

namespace JLM\OfficeBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * QuoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuoteRepository extends EntityRepository
{
	public function getTotal()
	{
		$qb = $this->createQueryBuilder('q')
		->select('COUNT(q)');
		
		return (int) $qb->getQuery()
		->getSingleScalarResult();
	}
	
	public function getLastNumber($year = null)
	{
		$year = 2012;
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
				->where('q.followerCp LIKE :query')
				->orWhere('q.number LIKE :query')
				->orWhere('q.trusteeName LIKE :query')
				->orWhere('q.trusteeAddress LIKE :query')
				->orWhere('q.doorCp LIKE :query')
				->orWhere('q.contactCp LIKE :query')
				->orWhere('q.followerCp LIKE :query')
				->orderBy('q.creation','DESC')
				->setParameter('query', '%'.$query.'%');
		
		return $qb->getQuery()->getResult();
	}
}