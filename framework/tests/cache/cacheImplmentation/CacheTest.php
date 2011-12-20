<?php
 /*Copyright 2011 Imre Toth <tothimre at gmail>

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

namespace unittest;
use POC\cache\filtering\Evaluateable;


abstract class CacheTest extends \PHPUnit_Framework_TestCase
{

  public $TESTKEY = 'testkey';
  const TESTDATA = 'testdata';
  const TTL = 1;

  public $cache = null;

  abstract function setUp_();

  protected function setUp() {
    $this->TESTKEY .= rand().rand();
    try{
      $this->setUp_();
    } catch (Exception $e) {
      $this->asertTrue(false);
    }
    $this->cache->cacheSpecificClearAll();
  }

  public function testCacheSpecificFetch() {
    $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
    $fetchedFromCache = $this->cache->cacheSpecificFetch($this->TESTKEY);
    $this->assertTrue($fetchedFromCache == self::TESTDATA);
    sleep(self::TTL+1);
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) == '');
  }

  public function testCacheSpecificClearAll() {
    $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
    $this->cache->cacheSpecificClearAll();
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) !=
                                                                self::TESTDATA);
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) == '');
  }

  public function testCacheSpecificClearItem() {
    $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
    $this->cache->cacheSpecificClearItem($this->TESTKEY);
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) !=
                                                                self::TESTDATA);
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) == '');

  }

  public function testCacheSpecificStore() {
    $this->cache->cacheSpecificStore($this->TESTKEY, self::TESTDATA);
    $this->assertTrue($this->cache->cacheSpecificFetch($this->TESTKEY) ==
                                                                self::TESTDATA);
  }
}
?>