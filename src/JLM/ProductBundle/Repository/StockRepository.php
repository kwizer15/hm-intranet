<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;
use JLM\ProductBundle\Model\ProductInterface;

/**
 * BillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class StockRepository extends EntityRepository
{
   
    public function getCount()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function getAll($limit = 10, $offset = 0)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a,b')
            ->leftJoin('a.product', 'b')
            ->orderBy('b.designation', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        
        return $qb->getQuery()->getResult();
    }
    
    public function getByProduct(ProductInterface $product)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.product = ?1')
            ->setParameter(1, $product)
        ;
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}
