<?php

namespace JLM\ModelBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SiteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoorRepository extends EntityRepository
{

	public function search($query)
	{
		$qb = $this->createQueryBuilder('d')
		->leftJoin('d.site','s')
		->leftJoin('s.address','a')
		->leftJoin('a.city','c')
		->where('a.street LIKE :querystreet')
		->orWhere('d.street LIKE :querystreet')
		->orWhere('c.name LIKE :querycity')
		->setParameter('querystreet', '%'.$query.'%')
		->setParameter('querycity', $query.'%')
		;
		
		return $qb->getQuery()->getResult();
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
}