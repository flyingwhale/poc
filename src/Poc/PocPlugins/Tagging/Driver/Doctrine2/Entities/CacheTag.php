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

namespace Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities;

/**
 * @Entity(repositoryClass="Poc\PocPlugins\Tagging\Driver\Doctrine2\Repositories\CacheTagRepository")
 * @Table(name="tags_has_caches")
 */
class CacheTag
{

    /**
     * @Id
     * @ManyToOne(targetEntity="Tag", inversedBy="tagCaches")
     */
    protected $tag;

    /**
     * @Id
     * @ManyToOne(targetEntity="Cache", inversedBy="cacheTags")
     */
    protected $cache;

    /**
     * Get cache
     *
     * @return integer
     */
    public function getCache ()
    {
        return $this->cache;
    }

    /**
     * Get tag
     *
     * @return integer
     */
    public function getTag ()
    {
        return $this->tag;
    }

    /**
     * Set cache
     *
     * @param $cache integer
     */
    public function setCache ($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Set tag
     *
     * @param $tag integer
     */
    public function setTag ($tag)
    {
        $this->tag = $tag;
    }

}
