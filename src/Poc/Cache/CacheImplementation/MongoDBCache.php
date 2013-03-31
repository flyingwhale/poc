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

/**
 * This is the MongoDB implemetation for caching.
 *
 * @author Imre Toth
 *
 */

namespace Poc\Cache\CacheImplementation;

use Poc\Exception\CacheNotReachableException;
use Poc\Exception\DriverNotFoundException;

class MongoDBCache extends Cache
{
    private $mongo;

    private $dbName;

    private $collectionName;

    protected function setupDefaults ()
    {
        parent::setupDefaults();
        $this->optionable->setDefaultOption('db_name', 'poc');
        $this->optionable->setDefaultOption('collection_name', 'key_value');
    }

    public function __construct ($options = array())
    {
        parent::__construct($options);

        $this->dbName = $this->optionable['db_name'];
        $this->collectionName = $this->optionable['collection_name'];
        $className = 'Mongo';

        // @codeCoverageIgnoreStart
        try {

            if (! class_exists($className)) {
                throw new DriverNotFoundException('Mongo driver not found');
            }
            $this->mongo = new $className();
        } catch (\MongoConnectionException $e) {
            throw new CacheNotReachableException('Mongo not reachable');
        }
        // @codeCoverageIgnoreEnd

    }

    public function fetch ($key)
    {
        $value = '';

        $keyValue = $this->findKeyValueObj($key);

        if ($keyValue && $keyValue['expire'] > time()) {
            $value = $keyValue['value'];
        }

        return $value;
    }

    public function clearAll ()
    {
        $keyValueCollection = $this->getCollection();
        $keyValueCollection->drop();

    }

    public function clearItem ($key)
    {
        $criteria = array('key' => $key);

        $keyValueCollection = $this->getCollection();
        $keyValueCollection->remove($criteria);

    }

    public function cacheSpecificStore ($key, $output)
    {
        $keyValueCollection = $this->getCollection();

        $keyValue = $this->findKeyValueObj($key);

        if (! $keyValue) {
            $keyValue = array('key' => $key, 'value' => $output, 'expire' => time() + $this->ttl);
        } else {
            $keyValue['value'] = $output;
            $keyValue['expire'] = time() + $this->ttl;
        }

        $keyValueCollection->save($keyValue);

    }

    private function getDb ()
    {
        $db = $this->mongo->selectDB($this->dbName);

        return $db;
    }

    private function getCollection ()
    {
        $collection = $this->getDb()->selectCollection($this->collectionName);

        return $collection;
    }

    private function findKeyValueObj ($key)
    {
        $keyValueCollection = $this->getCollection();

        $filter = array('key' => $key);

        $keyValue = $keyValueCollection->findOne($filter);

        return $keyValue;
    }
}