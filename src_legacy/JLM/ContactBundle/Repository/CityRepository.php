<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class CityRepository extends EntityRepository
{
    /**
     *
     * @param string $query
     * @param int    $limit
     *
     * @return array
     */
    public function searchResult($query, $limit = 8)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.zip LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', $query . '%')
        ;
        $res = $qb->getQuery()->getResult();
        $r2 = [];
        foreach ($res as $r) {
            $r2[] = '' . $r;
        }

        return $r2;
    }

    /**
     *
     * @param string $query
     * @param int    $limit
     *
     * @return array
     */
    public function getArray($query, $limit = 8)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.zip LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', $query . '%')
        ;
        $res = $qb->getQuery()->getArrayResult();

        return $res;
    }

    /**
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getByIdToArray($id)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
        ;
        $res = $qb->getQuery()->getArrayResult();

        if (isset($res[0])) {
            return $res[0];
        }

        return null;
    }
}
