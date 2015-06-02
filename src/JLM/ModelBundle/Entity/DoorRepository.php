<?php

namespace JLM\ModelBundle\Entity;

use JLM\DefaultBundle\Entity\SearchRepository;

/**
 * SiteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoorRepository extends SearchRepository
{
	
	public function getStopped($limit = null, $offset = null)
	{
		$qb = $this->createQueryBuilder('a')
			->select('a,b,c,d,e,f,g,h,i,j')
			->leftJoin('a.site','b')
			->leftJoin('b.address','c')
			->leftJoin('c.city','d')
			->leftJoin('a.interventions','e')
			->leftJoin('e.shiftTechnicians','f')
			->leftJoin('a.contracts','g')
			->leftJoin('g.trustee','h')
			->leftJoin('a.type','i')
			->leftJoin('a.stops','j')
			->where('j.end is null AND j.begin is not null')
			;
		if ($limit !== null)
			$qb->setMaxResults($limit);
		if ($offset !== null)
			$qb->setFirstResult($offset);
		return $qb->getQuery()->getResult();
	}
	
	public function getCountStopped()
	{
		$qb = $this->createQueryBuilder('a')
			->select('COUNT(a)')
			->leftJoin('a.stops','j')
			->where('j.end is null AND j.begin is not null')
			;
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getSearchQb()
	{
		return $this->createQueryBuilder('a')
		->leftJoin('a.site','b')
		->leftJoin('b.address','c')
		->leftJoin('c.city','d')
		;
	}
	
	/**
	 * Get by code
	 * 
	 * @return Door
	 */
	public function getByCode($code)
	{
		$qb = $this->createQueryBuilder('a')
			->select('a')
			->where('a.code = ?1')
			->setParameter(1,strtoupper($code))
		;
		
		try {
			return $qb->getQuery()->getSingleResult();
		} catch (\Exception $e) {}
		
		return null;
		 
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getSearchParams()
	{
		return array('c.street','a.street','d.name','a.code');
	}
	
	public function searchResult($query, $limit = 8)
	{
		
		$res = $this->search($query);
		
		// Structure
		// array(IdPorte,Affaire,IdSyndic,Syndic,Adresse de facturation)
		$r2 = array();
		foreach ($res as $r)
		{
			$trustee = $r->getSite()->getTrustee();
			$reference = '';
			if ($r->getSite()->getGroupNumber())
				$reference .= 'Groupe : '.$r->getSite()->getGroupNumber();
			$r2[] = array(
						'door'          => ''.$r->getId(),
						'label'        => ''.$r,
						'doorCp'		=> ''.$r->toString(),
						'vat'			=> $r->getSite()->getVat()->getRate(),
						'trustee'       => ''.$trustee->getId(),
						'trusteeName'   => ''.$trustee,
						'trusteeBillingLabel'   => ''.$trustee->getBillingLabel(),
						'trusteeAddress'=> ''.$trustee->getAddress()->toString(),
						'trusteeBillingAddress'=> ''.$trustee->getAddressForBill()->toString(),
						'accountNumber'=> $trustee->getAccountNumber(),
						'doorDetails' => $r->getType().' - '.$r->getLocation(),
						'siteCp'=> $r->getSite()->toString(),
						'prelabel'=> $r->getBillingPrelabel(),
						'reference'=>$reference,
					);
		}
		return $r2;
	}
	
	public function getTotal()
	{
		$qb = $this->createQueryBuilder('d')
		->select('COUNT(d)');
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	public function getCountByType($year = 2015)
	{
		$qb = $this->createQueryBuilder('a')
		->select('i.name as name,COUNT(a) as nb')
		->leftJoin('a.contracts','g')
		->leftJoin('a.type','i')
		->where('g is not null and g.end is null')
		->groupBy('i.name')
		->orderBy('nb','DESC')
		;
		
		return $qb->getQuery()->getResult();
	}
	
	public function getCountIntervsByType($year = 2015, $type = 'fixing')
	{
		$types = array(
				'fixing'=>'JLM\DailyBundle\Entity\Fixing',
				'maintenance'=>'JLM\DailyBundle\Entity\Maintenance',
				'work'=>'JLM\DailyBundle\Entity\Work',	
		);
		$qb = $this->createQueryBuilder('a')
		->select('i.name as name,COUNT(b) as nb, sum(time_to_sec(c.end) - time_to_sec(c.begin)) as time')
		->leftJoin('a.contracts','g')
		->leftJoin('a.type','i')
		->leftJoin('a.interventions','b')
		->leftJoin('b.shiftTechnicians','c')
		->where('g is not null and g.end is null and b INSTANCE OF '.$types[$type].' and c.end BETWEEN \''.$year.'-01-01 00:00:00\' AND \''.$year.'-12-31 23:59:59\'')
		->groupBy('i.name')
		->orderBy('nb','DESC')
		;
	
		return $qb->getQuery()->getResult();
	}
}