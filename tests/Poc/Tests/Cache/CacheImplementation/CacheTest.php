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

namespace Poc\Tests\Cache\CacheImplementation;

use Poc\Cache\Filtering\Hasher;

abstract class CacheTest extends \PHPUnit_Framework_TestCase
{

    const TESTDATA = 'testdata';

    const TTL = 1;

    public $cache = null;

    public $TESTKEY = 'testkey';

    public $hasher;

    abstract public function setUp_ ();

    protected function setUp ()
    {
        $this->hasher = new Hasher();

        $this->TESTKEY .= rand() . rand();
        try {
            $this->setUp_();
        } catch (Exception $e) {
            $this->asertTrue(false);
        }
        $this->cache->clearAll();
    }

    public function testCacheSpecificFetch ()
    {
        $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
        $fetchedFromCache = $this->cache->fetch($this->TESTKEY);
        $this->assertTrue($fetchedFromCache == self::TESTDATA);
        sleep(self::TTL + 1);
        $this->assertTrue($this->cache->fetch($this->TESTKEY) == '');
    }

    public function testCacheSpecificClearAll ()
    {
        $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
        $this->cache->clearAll();
        $this->assertTrue(
                $this->cache->fetch($this->TESTKEY) != self::TESTDATA);
        $this->assertTrue($this->cache->fetch($this->TESTKEY) == '');
    }

    public function testCacheSpecificClearItem ()
    {
        $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
        $this->cache->clearItem($this->TESTKEY);
        $this->assertTrue(
                $this->cache->fetch($this->TESTKEY) != self::TESTDATA);
        $this->assertTrue($this->cache->fetch($this->TESTKEY) == '');

    }

    public function testCacheSpecificStore ()
    {
        $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
        $this->assertTrue(
                $this->cache->fetch($this->TESTKEY) == self::TESTDATA);
    }
}
?>
