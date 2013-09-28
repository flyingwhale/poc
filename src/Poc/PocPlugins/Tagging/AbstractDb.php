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

namespace Poc\PocPlugins\Tagging;

use Poc\Core\PluginSystem\PluginInterface;
use Poc\Core\PocEvents\PocEventNames;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandlerEventNames;

abstract class AbstractDb implements PluginInterface
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

    abstract public function addCacheToTags($tags);

    abstract public function flushOutdated();

    abstract public function tagInvalidate($tags);

    public function pluginInit($poc)
    {

        $this->poc = $poc;
        $this->cache = $poc->getCache();
        $this->ttl = $poc->getCache()->getTtl();
        $this->hash = $poc->getHasher()->getKey();

        $poc->getPocDispatcher()->addListener(CallbackHandlerEventNames::OUTPUT_STORED,
                                                  array($this, 'cacheAddTags'));
        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                    array($this, 'cacheTagsInvalidation'));

    }

    public function isPluginMultipleInstanced()
    {
        return false;
    }

    // @codeCoverageIgnoreStart
    public function setCache ($cache)
    {
        $this->cache = $cache;
    }

    public function addCacheInvalidationTags ($condition, $tags)
    {
        if ($condition) {
             $this->cacheInvalidationTags[] = $tags;
        }
    }

    public function addCacheAddTags ($condition, $tags)
    {
        if ($condition) {
            $this->cacheAddTags[] = $tags;
        }
    }

    public function cacheAddTags()
    {
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
    // @codeCoverageIgnoreEnd
}
