<?php

namespace JLM\ModelBundle\Entity;

use JLM\DefaultBundle\Entity\SearchRepository;

/**
 * SupplierRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SupplierRepository extends SearchRepository
{
	/**
	 * {@inheritdoc}
	 */
	protected function getSearchQb()
	{
		return $this->createQueryBuilder('a');
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getSearchParams()
	{
		return array('a.name');
	}
	
	
	
	public function searchResult($query, $limit = 8)
	{
		$res = $this->search($query);
		$r2 = array();
		foreach ($res as $r)
			$r2[] = ''.$r;
		return $r2;
	}
	
	public function getTotal()
	{
		$qb = $this->createQueryBuilder('s')
				->select('COUNT(s)');
		
		return (int) $qb->getQuery()
				->getSingleScalarResult();
	}
}