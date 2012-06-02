<?php
/*
 * Copyright 2011 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */
/**
 * This is the root class of the Cache engines that the framework implements.
 *
 * @author Imre Toth
 *
 */
namespace Poc\Cache\CacheImplementation;

use Poc\Poc;
use Poc\Cache\Tagging\AbstractDb;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\Filtering\Filter;
use Poc\Cache\Filtering\Hasher;
use Poc\Core\OptionAble\OptionAble;
use Poc\Cache\Tagging\Tagger;
use Poc\Core\OptionAble\OptionAbleInterface;

abstract class Cache implements CacheInterface, OptionAbleInterface, CacheParams
{

    /**
     * This variable must be declared at the constructors of this class.
     */
    protected $ttl;

    /**
     * The database that stores the caches
     *
     * @var AbstractDb
     *
     */
    private $tagDb;

    protected $defaultOptions = array();

    /**
     *
     * @var Hasher
     */
    protected $hasher;

    /**
     *
     * @var Filter
     */
    protected $filter;

    protected $cacheInvalidationTags = array();

    protected $cacheAddTags = array();

    /**
     *
     * @var OptionAble
     */
    protected $optionAble = null;

    public function fillDefaults ()
    {
        $this->optionAble[self::PARAM_HASHER] = function  () {
            return new Hasher();
        };

        $this->optionAble[self::PARAM_FILTER] = function  () {
            return new Filter();
        };

        $this->optionAble[self::PARAM_TTL] = 5;
        $this->optionAble[self::PARAM_TAGDB] = function  () {
            return null;
        };
    }

    public function __construct ($options)
    {
        $this->optionAble = new OptionAble($options, $this);
        $this->optionAble->start();
        $this->hasher = $this->optionAble->getOption(CacheParams::PARAM_HASHER);
        $this->ttl = $this->optionAble->getOption(CacheParams::PARAM_TTL);
        $this->tagDb = $this->optionAble->getOption(CacheParams::PARAM_TAGDB);
        $this->filter = $this->optionAble->getOption(CacheParams::PARAM_FILTER);
    }

    /**
     *
     * @return Filter
     */
    public function getFilter ()
    {
        return $this->filter;
    }

    public function addCacheInvalidationTags ($condition, $tags)
    {
        if (! $this->tagDb) {
            throw new \Exception("Please Define a TagDb");
        } else {
            if ($condition) {
                $tagger = new Tagger($tags, $this->hasher, $this->tagDb,
                        $this->ttl);
                $tagger->addCache($this);
                $this->cacheInvalidationTags[] = $tagger;
            }
        }
    }

    public function addCacheAddTags ($condition, $tags)
    {
        if (! $this->tagDb) {
            throw new \Exception("Please Define a TagDb");
        } else {
            if ($condition) {
                $tagger = new Tagger($tags, $this->hasher, $this->tagDb,
                        $this->ttl);
                $tagger->addCache($this);
                $this->cacheAddTags[] = $tagger;
            }
        }
    }

    public function cacheAddTags ()
    {
        foreach ($this->cacheAddTags as $tagger) {
            $tagger->tagCache();
        }
    }

    public function cacheTagsInvalidation ()
    {
        foreach ($this->cacheInvalidationTags as $tagger) {
            $tagger->cacheInvalidation();
        }
    }

    /**
     *
     * @return Hasher
     */
    public function getHasher ()
    {
        return $this->hasher;
    }

    public function getTagDb ()
    {
        return $this->tagDb;
    }

    public function throwDbException ()
    {
        if (! $this->isCacheAvailable()) {
            throw new \Exception("The cache database is not reachable!");
        }
    }
    
    public function getTtl(){
        return $this->ttl;
    }
    
}
