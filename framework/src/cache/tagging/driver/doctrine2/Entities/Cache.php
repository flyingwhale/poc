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

namespace Entities;

/**
 * @Entity(repositoryClass="Repositories\CacheRepository")
 * @Table(name="caches")
 */
class Cache
{

    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @OneToMany(targetEntity="CacheTag", mappedBy="cache", orphanRemoval=true,
     * cascade={"persist"})
     */
    protected $cacheTags;

    /**
     * @Column(type="integer")
     */
    protected $expires;

    /**
     * @Column(type="string")
     */
    protected $hash;

    public function __construct ()
    {
        $this->cacheTags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cacheTags
     *
     * @param $cacheTags Entities\CacheTag           
     */
    public function addCacheTag (\Entities\CacheTag $cacheTags)
    {
        $this->cacheTags[] = $cacheTags;
    }

    public function addTag ($tag)
    {
        $cacheTag = new \Entities\CacheTag();
        $cacheTag->setCache($this);
        $cacheTag->setTag($tag);
        
        $this->addCacheTag($cacheTag);
        $tag->addCacheTag($cacheTag);
    }

    /**
     * Get cacheTags
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCacheTags ()
    {
        return $this->cacheTags;
    }

    /**
     * Get expires
     *
     * @return integer
     */
    public function getExpires ()
    {
        return $this->expires;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash ()
    {
        return $this->hash;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Set expires
     *
     * @param $expires integer           
     */
    public function setExpires ($expires)
    {
        $this->expires = $expires;
    }

    /**
     * Set hash
     *
     * @param $hash string           
     */
    public function setHash ($hash)
    {
        $this->hash = $hash;
    }

}