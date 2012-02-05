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

class CacheTagRepository extends EntityRepository
{
    public function getExpired($expires = null)
    {
        if (!$expires)
        {
        	$expires = time();
        }
        
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('ct')
        ->from('Entities\\CacheTag', 'ct')
        ->leftJoin('ct.cache', 'c')
        ->leftJoin('ct.tag', 't')
        ->where('c.expires < :expires')
        ->setParameter('expires', $expires);
        
        return $qb->getQuery()->getResult();
    }

    public function orphanRemoval()
    {
    	$queryString = 'DELETE ths FROM tags_has_caches ths  LEFT JOIN tags t  ON (t.id = ths.tag_id) LEFT JOIN caches c  ON (c.id = ths.cache_id) WHERE  t.id IS NULL OR c.id IS NULL';
    
    	$conn = $this->_em->getConnection();
    	$rowsAffected = $conn->executeUpdate($queryString);
    
    	return $rowsAffected;
    }
}
?>