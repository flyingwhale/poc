<?php
/*Copyright 2012 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

namespace Repositories;

use Doctrine\ORM\EntityRepository;

class CacheRepository extends EntityRepository
{

    public function getByTags ($tags)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('c')
            ->from('Entities\Cache', 'c')
            ->leftJoin('c.cacheTags', 'ct')
            ->leftJoin('ct.tag', 't')
            ->where($qb->expr()
            ->in('t.tag', $tags));
        
        return $qb->getQuery()->getResult();
    }

    public function getByTagAndHash ($tag, $hash)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('c')
            ->from('Entities\Cache', 'c')
            ->leftJoin('c.cacheTags', 'ct')
            ->leftJoin('ct.tag', 't')
            ->where('t.tag = :tag')
            ->andWhere('c.hash = :hash')
            ->setParameter('tag', $tag)
            ->setParameter('hash', $hash);
        
        return $qb->getQuery()->getResult();
    }

    public function orphanRemoval ()
    {
        $queryString = 'DELETE c FROM caches  c LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id) WHERE thc.cache_id IS NULL';
        
        $conn = $this->_em->getConnection();
        $rowsAffected = $conn->executeUpdate($queryString);
        
        return $rowsAffected;
    }
}
?>