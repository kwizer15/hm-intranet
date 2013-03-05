<?php

namespace JLM\ModelBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SiteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SiteRepository extends EntityRepository
{
	public function search($query)
	{
		$qb = $this->createQueryBuilder('s')
		->leftJoin('s.address','a')
		->leftJoin('a.city','c')
		->where('a.street LIKE :querystreet')
		->orWhere('c.name LIKE :querycity')
		->setParameter('querystreet', '%'.$query.'%')
		->setParameter('querycity', $query.'%')
		;
		
		return $qb->getQuery()->getResult();
	}
	
	public function searchResult($query, $limit = 8)
	{
		$res = $this->search($query);
		
		
		$r2 = array();
		foreach ($res as $r)
		{
			$reference = '';
			if ($r->getGroupNumber())
				$reference .= 'Groupe : '.$r->getGroupNumber();
			foreach ($r->getDoors() as $d)
				$doorDetails = $d->getType().' - '.$d->getLocation().chr(10);
			$r2[] = array(
					'label'=>''.$r,
					'siteCp'=>''.$r->toString(),
					'trustee'=>''.$r->getTrustee()->getId(),
					'trusteeName'=>''.$r->getTrustee()->getName(),
					'trusteeAddress'=>''.$r->getTrustee()->getAddress()->toString(),
					'trusteeBillingAddress'=>''.$r->getTrustee()->getAddressForBill()->toString(),
					'accountNumber'=>$r->getTrustee()->getAccountNumber(),
					'prelabel'=>$r->getBillingPrelabel(),
					'vat'=>$r->getVat()->getRate(),
					'vatid'=>$r->getVat()->getId(),
					'doorDetails'=>$doorDetails,
					'reference'=>$reference,
				);
		}
		return $r2;
	}
	
	public function match($string)
	{
		if (preg_match('#^([\w\-\/",.\'âêîôûéèà\s]+)\s([0-9AB]{5}( CEDEX)?) - (.+)$#',$string,$matches))
		{
			$qb = $this->createQueryBuilder('s')
				->leftJoin('s.address','a')
				->leftJoin('a.city','c')
				->where('a.street LIKE :querystreet')
				->andwhere('c.zip = :queryzip')
				->andWhere('c.name = :querycity')
				->setParameter('querystreet', trim($matches[1]).'%')
				->setParameter('queryzip', $matches[2])
				->setParameter('querycity', $matches[4])
				;
				
			$res = $qb->getQuery()->getResult();
			
			if ($res)
				return $res[0];
		}
		else
		{
			return null;
		}
			
	}
}