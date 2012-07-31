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
 * This class utilizes the REDIS key-value database for caching.
 *
 * @author Imre Toth
 *
 */

namespace Poc\Cache\CacheImplementation;

class PredisCache extends Cache
{

    protected $redis;

    protected $isNotConnected;

    protected function setupDefaults ()
    {
        parent::setupDefaults();
        $this->optionable->setDefaultOption('servers', array('servers' => array(array('host' => 'localhost', 'port' => '6379'))));
    }

    public function __construct ($options = array())
    {
        parent::__construct($options);

        $className = 'Predis\Client';
        if (! class_exists($className)) {
            throw new \Exception(sprintf("%s class not exists", $className));
        }

        $this->redis = new $className($this->optionable['servers']);
        $this->isNotConnected = 1;
    }

    public function fetch ($key)
    {
        $value = $this->redis->get($key);

        return $value;
    }

    public function clearAll ()
    {
        $this->redis->flushdb();
    }

    public function clearItem ($key)
    {
        $this->redis->del($key);
    }

    public function cacheSpecificStore ($key, $output)
    {
        $this->redis->set($key, $output);
        $this->redis->expire($key, $this->ttl);
    }

    public function isCacheAvailable ()
    {
        return $this->isNotConnected;
    }

}
