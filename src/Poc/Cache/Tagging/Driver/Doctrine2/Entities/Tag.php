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
 * @Entity(repositoryClass="Repositories\TagRepository")
 * @Table(name="tags")
 */
class Tag
{

    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $tag;

    /**
     * @OneToMany(targetEntity="CacheTag", mappedBy="tag", orphanRemoval=true,
     * cascade={"persist"})
     */
    protected $tagCaches;

    public function __construct ()
    {
        $this->tagCaches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tagCaches
     *
     * @param $tagCaches Entities\CacheTag           
     */
    public function addCacheTag (\Entities\CacheTag $tagCaches)
    {
        $this->tagCaches[] = $tagCaches;
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
     * Get tag
     *
     * @return string
     */
    public function getTag ()
    {
        return $this->tag;
    }

    /**
     * Get tagCaches
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTagCaches ()
    {
        return $this->tagCaches;
    }

    /**
     * Set tag
     *
     * @param $tag string           
     */
    public function setTag ($tag)
    {
        $this->tag = $tag;
    }

}