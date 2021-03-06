<?php
/*
 * Copyright 2013 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\PocPlugins\Tagging\Driver\Doctrine2\Repositories;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{

    public function orphanRemoval ()
    {
        $queryString = 'DELETE t FROM tags  t LEFT JOIN tags_has_caches thc  ON (t.id = thc.tag_id) WHERE thc.tag_id IS NULL';

        $conn = $this->_em->getConnection();
        $rowsAffected = $conn->executeUpdate($queryString);

        return $rowsAffected;
    }

    public function removeByName($name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete('\\Poc\\PocPlugins\\Tagging\\Driver\\Doctrine2\\Entities\\Tag', 't')
            ->where('t.tag = :name')
            ->setParameter('name', $name);

        return $qb->getQuery()->getResult();
    }

    public function removeByNames($names)
    {
        foreach ($names as $name) {
            $this->removeByName($name);
        }
    }

}
