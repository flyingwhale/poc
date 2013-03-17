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

/**
 * This cacheengine stores the caches in the Memcached engine.
 * In most cases it is the preferred solution, because this engine
 * is the most popular for caching from the list the framework supports.
 *
 * @author Imre Toth
 *
 */

namespace Poc\Cache\CacheImplementation;

class MemcachedCache extends Cache
{

    private $memcache;

    private $compression = false;

    private $isConnected;

    protected function setupDefaults ()
    {
        parent::setupDefaults();
        $this->optionable->setDefaultOption('server', 'localhost');
        $this->optionable->setDefaultOption('port', '11211');
    }

    public function __construct ($options = array())
    {
        parent::__construct($options);

        $className = 'Memcache';

        // @codeCoverageIgnoreStart
        // @codeCoverageIgnoreEnd
        // @codeCoverageIgnoreStart
        if (! class_exists($className)) {
            throw new \Exception(sprintf("%s class not exists", $className));
        }
        // @codeCoverageIgnoreEnd
        $this->memcache = new $className();

        $this->isConnected = $this->memcache->connect(
                $this->optionable['server'],
                $this->optionable['port']);
        $this->throwDbException();
    }

    public function fetch ($key)
    {
        return $this->memcache->get($key);
    }

    public function clearAll ()
    {
        $this->memcache->flush();
    }

    public function clearItem ($key)
    {
        $this->memcache->delete($key);
    }

    public function cacheSpecificStore ($key, $output)
    {
        $this->memcache->set($key, $output, $this->compression, $this->ttl);
    }

    public function isCacheAvailable ()
    {
        return $this->isConnected;
    }

}
