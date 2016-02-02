<?php

namespace JLM\ContractBundle\Repository;

use Doctrine\ORM\EntityRepository;
use JLM\StateBundle\Entity\Calendar;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContractRepository extends EntityRepository
{
	public function searchResult($query, $limit = 8)
	{
		$qb = $this->createQueryBuilder('c')
			   ->where('c.number LIKE :query')
			   ->setParameter('query', $query.'%')
		;
		$res = $qb->getQuery()->getResult();
		$r2 = array();
		foreach ($res as $r)
		{
			$r2[] = $r->getNumber().' / '.$r->getDoor()->getLocation().' / '.$r->getDoor()->getType();
		}
		return $r2;
	}
	
	public function getStatsByDates()
	{
		$today = new \DateTime;
		$em = $this->getEntityManager();
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('dt', 'date');
		$rsm->addScalarResult('number', 'number');
		$rsm->addScalarResult('acc', 'accession');
		$rsm->addScalarResult('comp', 'complete');
		$q = '
			SELECT b.dt, COUNT(a.id) as number,d.accession as acc,a.complete as comp
			FROM jlm_core_calendar b
			LEFT JOIN contracts a ON b.dt > a.begin AND (b.dt < a.end_contract OR a.end_contract IS NULL)
			LEFT JOIN doors c ON a.door_id = c.id
			LEFT JOIN sites d ON c.site_id = d.id
			WHERE b.dt < ?  GROUP BY b.dt,d.accession,a.complete';
		$query = $em->createNativeQuery($q, $rsm);
		$query->setParameter(1, $today);
		
		return $query->getResult();
	}
	
	public function getStatsByMonth($year = null)
	{
		$em = $this->getEntityManager();
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('year', 'year');
		$rsm->addScalarResult('month', 'month');
		$rsm->addScalarResult('number', 'number');
		$q = '
			SELECT YEAR(b.dt) as year, MONTH(b.dt) as month, COUNT(DISTINCT a.id) as number
			FROM jlm_core_calendar b
			LEFT JOIN contracts a ON 
				(
					(YEAR(b.dt) = YEAR(a.begin) AND MONTH(b.dt) >= MONTH(a.begin))
					OR (YEAR(b.dt) > YEAR(a.begin))
				)
				AND (
					(YEAR(b.dt) = YEAR(a.end_contract) AND MONTH(b.dt) <= MONTH(a.end_contract))
					OR (YEAR(b.dt) < YEAR(a.end_contract))
					OR a.end_contract IS NULL
				)
			WHERE b.dt < ? GROUP BY YEAR(b.dt), MONTH(b.dt)
			ORDER BY YEAR(b.dt) ASC, MONTH(b.dt) ASC	
				';
		$query = $em->createNativeQuery($q, $rsm);
		$query->setParameter(1, new \DateTime);
		
		return $query->getResult();
	}
	
	public function getForRDV()
	{
		$qb = $this->createQueryBuilder('a')
			->select('f.name as manager, COUNT(a) as count_installs, g.name as gestionnaire')
				->leftJoin('a.door','b')
					->leftJoin('b.site','c')
						->leftJoin('c.contacts','d')
							->leftJoin('d.person','g')
				->leftJoin('a.trustee','e')
					->leftJoin('e.contact','f')
			->where('a.begin <= ?1')
			->andWhere('a.end IS NULL OR a.end > ?1')
			//->andWhere('d.role LIKE ?2 OR d IS NULL')
			->groupBy('e')

			->setParameter(1, new \DateTime())
			->setParameter(2, 'gestionnaire')
		;
		
		$query = $qb->getQuery();
		
		return $query->getArrayResult();
	}
}