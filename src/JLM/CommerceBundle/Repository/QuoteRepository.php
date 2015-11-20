<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Repository;

use JLM\ModelBundle\Entity\Door;
use JLM\DefaultBundle\Entity\SearchRepository;
use JLM\CommerceBundle\Entity\QuoteVariant;
use JLM\CommerceBundle\Entity\Quote;

/**
 * QuoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * 
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class QuoteRepository extends SearchRepository
{
	/**
	 * 
	 * @param int $id
	 * @return Quote
	 */
	public function getById($id)
	{
		$qb = $this->createQueryBuilder('a')
			->select('a')
			->leftJoin('a.door','b')
			->leftJoin('b.site','c')
			->leftJoin('c.address','d')
			->leftJoin('d.city','e')
			->leftJoin('b.type','f')
			->leftJoin('a.contact','g')
			->leftJoin('a.contactPerson','h')
			->leftJoin('a.variants','i')
			->leftJoin('i.lines','j')
			->where('a.id = ?1')
			
			->setParameter(1, $id);
		
		return $qb->getQuery()->getSingleResult();
	}
	
	/**
	 * 
	 */
	public function getTotal()
	{
		return $this->getCountState();
	}
	
	/**
	 * 
	 * @return number
	 */
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
		
		return (!$result) ? 0 : $result[0]['num'];
	}
	
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
		return array('a.number','a.trusteeName','a.trusteeAddress','a.doorCp','a.contactCp');
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getSearchOrderBy()
	{
		return array('a.number'=>'DESC');
	}

	/**
	 * 
	 * @param string $state
	 * @param string $year
	 */
	public function getCountState($state = null, $year = null)
	{
		if (!isset($this->countState[$year]))
		{
			$year = ($year === null) ? 'total' : $year;
			$qb = $this->createQueryBuilder('a')
				->select('a,b,c')
				->leftJoin('a.variants','b')
				->leftJoin('a.contactPerson','c')
				;
			if ($year !== 'total')
			{
				$qb->where('a.creation BETWEEN :fd AND :ld')
					->setParameter('fd', $year.'-01-01')
					->setParameter('ld', $year.'-12-31')
				;
			}

			$result = $qb->getQuery()->getResult();
			$this->countState[$year] = array(0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0);
			$this->total[$year] = 0;
			$this->uncanceled[$year] = 0;
			foreach ($result as $r)
			{
				if (!isset($this->countState[$year][$r->getState()]))
					$this->countState[$year][$r->getState()] = 0;
				switch ($r->getState())
				{
					case 1:
					case 2:
						$this->countState[$year][1]++;
						$this->countState[$year][2]++;
						break;
					case 3:
					case 4:
						$this->countState[$year][3]++;
						$this->countState[$year][4]++;
						break;
					default:
						$this->countState[$year][$r->getState()]++;
				}
				if ($r->getState() >= 0)	// On retire les devis annulés
				{
					$this->uncanceled[$year]++;
				}
				$this->total[$year]++;
			}
		}
		
		return ($state === 'uncanceled') ? $this->uncanceled[$year] : (($state === null) ? $this->total[$year] : $this->countState[$year][$state]);
	}
	
	/**
	 * 
	 * @param int $state
	 * @param int $limit
	 * @param int $offset
	 * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, \Doctrine\Common\Cache\mixed>
	 */
	public function getByState($state, $limit = 10, $offset = 0)
	{
		if (isset($this->byState))
		{
			return $this->byState;
		}
			
		$state = ($state == 1 || $state == 2) ? array(1,2) : (($state == 3 || $state == 4) ? array(3,4) : array($state));
		$qb = $this->createQueryBuilder('a')
				->select('a,b,c,d,e,f,g,h')
				->leftJoin('a.door','b')
				->leftJoin('b.site','c')
				->leftJoin('c.address','d')
				->leftJoin('d.city','e')
				->leftJoin('b.type','f')
				->leftJoin('a.contact','g')
				->leftJoin('a.contactPerson','h')
				->orderBy('a.number','desc')
		;
		$results = $qb->getQuery()->getResult();
		foreach ($results as $key=>$r)
		{
			if (in_array($r->getState(),$state))
			{
				if ($offset)
				{
					unset($results[$key]);
					$offset--;
				}
				elseif (!$limit)
				{
					unset($results[$key]);
				}
				else
				{ 
					$limit--;
				}
			}
			else
			{
				unset($results[$key]);
			}
		}
		$this->byState = $results;
		
		return $results;
	}
	
	/**
	 * 
	 * @param string $limit
	 * @param string $offset
	 * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, \Doctrine\Common\Cache\mixed>
	 */
	public function getAll($limit = null,$offset = null)
	{
		$qb = $this->createQueryBuilder('a')
			->select('a,b,c,d,e,f')
			->leftJoin('a.door','b')
			->leftJoin('b.site','c')
			->leftJoin('c.address','d')
			->leftJoin('d.city','e')
			->leftJoin('b.type','f')
			->orderBy('a.number','desc');
		if ($limit !== null)
		{
			$qb->setMaxResults($limit);
		}
		if ($offset !== null)
		{
			$qb->setFirstResult($offset);
		}
		
		return $qb->getQuery()->getResult();
	}

	public function getCountAll()
	{
		return $this->getTotal();
	}
	
	public function getCountInSeizure()
	{
		return $this->getCountState(0);
	}
	
	public function getInSeizure($limit = 10, $offset = 0)
	{
		return $this->getByState(0, $limit, $offset);
	}
	
	public function getCountWaiting()
	{
		return $this->getCountState(1);
	}
	
	public function getWaiting($limit = 10, $offset = 0)
	{
		return $this->getByState(1, $limit, $offset);
	}
	
	public function getCountSended()
	{
		return $this->getCountState(3);
	}
	
	public function getSended($limit = 10, $offset = 0)
	{
		return $this->getByState(3, $limit, $offset);
	}
	
	public function getCountGiven()
	{
		return $this->getCountState(5);
	}
	
	public function getGiven($limit = 10, $offset = 0)
	{
		return $this->getByState(5, $limit, $offset);
	}
	
	public function getCountCanceled()
	{
		return $this->getCountState(-1);
	}
	
	public function getCanceled($limit = 10, $offset = 0)
	{
		return $this->getByState(-1, $limit, $offset);
	}
	
	/**
	 * 
	 * @param Door $door
	 * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, \Doctrine\Common\Cache\mixed>
	 */
	public function getByDoor(Door $door)
	{
		$qb = $this->createQueryBuilder('a')
			->select('a')
			->leftJoin('a.door','b')
			->where('b.id = ?1')
			->orderBy('a.creation','desc')
			->setParameter(1,$door->getId());
		
		return $qb->getQuery()->getResult();
	}
	
	/**
	 * Get lasts sended quotes
	 * @param Door $door
	 * @since 1.4.0
	 * @todo Create a direct request
	 */
	public function getSendedByDoor(Door $door)
	{
		$quotes = $this->getByDoor($door);
		$final = [];
		foreach ($quotes as $quote)
		{
			if ($quote->getState() == QuoteVariant::STATE_SENDED)
			{
				$final[] = $quote;
			}
		}
		
		return $final;
	}
	
	public function getCountFollow()
	{
		return $this->getTotal();
	}
	
	public function getFollow($limit = 10, $offset = 0)
	{
		$qb = $this->createQueryBuilder('a')
		    ->select('a,b')
		    ->leftJoin('a.eventFollower','b')
		    ->orderBy('a.number','DESC');
		$qb->setMaxResults(10);
		
		return $qb->getQuery()->getResult();
	}

	public function getSends($year)
	{
		$qb = $this->createQueryBuilder('a')
			->select('MONTH(a.creation) AS month, COUNT(DISTINCT a) AS nb, SUM(l.unitPrice*l.quantity*(1-l.discount)) AS val')
			->leftJoin('a.variants','c')
			->leftJoin('c.lines','l')
			->where('c.state >= ?1')
			->andWhere('YEAR(a.creation) = ?2')
			->groupBy('month')
			->setParameter(1, QuoteVariant::STATE_SENDED)
			->setParameter(2, $year);
			
		return $qb->getQuery()->getResult();
	}
	
	public function getGivens($year)
	{
		$qb = $this->createQueryBuilder('a')
			->select('MONTH(d.creation) AS month, COUNT(DISTINCT a) AS nb, SUM(l.unitPrice*l.quantity*(1-l.discount)) AS val')
			->leftJoin('a.variants','c')
			->leftJoin('c.lines','l')
			->leftJoin('c.work','d')
			->where('c.state = ?1')
			->andWhere('YEAR(d.creation) = ?2')
			->groupBy('month')
			->setParameter(1, QuoteVariant::STATE_GIVEN)
			->setParameter(2, $year);
			
		return $qb->getQuery()->getResult();
	}
}