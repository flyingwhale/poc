<?php
/*
 * Copyright 2012 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\PocPlugins\Tagging;

use Poc\Core\PluginSystem\Plugin;
use Poc\Poc;
use Poc\PocEvents\PocEventNames;

abstract class AbstractDb extends Plugin
{

    protected $tags;

    protected $cacheInvalidationTags = array();

    protected $cacheAddTags = array();

    protected $hash;

    protected $ttl;

    /**
     *
     * @var \Poc\Cache\CacheImplementation\Cache
     */
    protected $cache;

    abstract public function addCacheToTags ($tags);

    abstract public function flushOutdated ();

    abstract public function tagInvalidate ($tags);

    public function init(Poc $poc)
    {
        parent::init($poc);
        $this->cache = $poc->getCache();
        $this->ttl = $poc->getCache()->getTtl();
        $this->hash = $poc->getHasher()->getKey();

        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                  array($this, 'cacheAddTags'));
        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINING,
                                                    array($this, 'cacheTagsInvalidation'));

    }

    public function __construct ()
    {
        $this->flushOutdated();
    }

    public function setCache ($cache)
    {
        $this->cache = $cache;
    }

    public function addCacheInvalidationTags ($condition, $tags)
    {
        echo'addCacheInvalidationTags';
        if ($condition) {
             echo'addCacheInvalidationTags';var_dump($tags);
             $this->cacheInvalidationTags[] = $tags;
        }
    }

    public function addCacheAddTags ($condition, $tags)
    {
        echo'addCacheInvalidationTags addCacheAddTags';
        if ($condition) {
            echo'addCacheAddTags';var_dump($tags);

            $this->cacheAddTags[] = $tags;
        }
    }

    public function cacheAddTags ()
    {
        echo "\n\n".'add:';
        var_dump($this->cacheAddTags);
        echo "\n\n";

        foreach ($this->cacheAddTags as $tags) {
            $this->addCacheToTags($tags);
        }
    }

    public function cacheTagsInvalidation ()
    {
        foreach ($this->cacheInvalidationTags as $tags) {
            $this->tagInvalidate($tags);
        }

    }
}
