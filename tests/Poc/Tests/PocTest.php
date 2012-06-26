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

namespace Poc\Tests;

use Poc\Tests\PocTestCore;

use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\PredisCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Handlers\Output\TestOutput;

class PocTest extends PocTestCore
{

    public function testBasicPocFunctionality ()
    {
        $objects = new \Pimple();
        $ttl = self::$TTL;

        $objects['file'] = function  () use ($ttl) {
            return new FileCache(array(CacheParams::PARAM_TTL => $ttl));
        };

        $objects['memcached'] = function  () use ($ttl) {
            return new MemcachedCache(
                    array(CacheParams::PARAM_TTL => $ttl));
        };

        $objects['predis'] = function  () use ($ttl) {

            return new PredisCache(array(CacheParams::PARAM_TTL => $ttl));
        };

        $objects['mongo'] = function  () use ($ttl) {

            return new MongoDBCache(array(CacheParams::PARAM_TTL => $ttl));
        };

        $handlers[] = 'file';
        $handlers[] = 'memcached';
        $handlers[] = 'predis';
        $handlers[] = 'mongo';

        foreach ($handlers as $cacheHandlerName) {
            $cacheHandler = $objects[$cacheHandlerName];

            $hasher = new Hasher();
            $hasher->addDistinguishVariable($cacheHandlerName.rand());

            $poc1 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                                  Poc::PARAM_OUTPUTHANDLER => new TestOutput(),
                                  Poc::PARAM_HASHER => $hasher));

            $this->pocBurner($poc1, self::TESTSTRING1);
            $output1 = $this->getOutput();

            for ($i = 0; $i < 10; $i ++) {
                $poc1 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                                    Poc::PARAM_OUTPUTHANDLER => new TestOutput(),
                                    Poc::PARAM_HASHER => $hasher ));
                $this->pocBurner($poc1, self::TESTSTRING1 . "Whatever $i");

            }

            $poc1 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                                  Poc::PARAM_OUTPUTHANDLER => new TestOutput(),
                                  Poc::PARAM_HASHER => $hasher ));
            $this->pocBurner($poc1, self::TESTSTRING2);
            $output2 = $this->getOutput();

            sleep(self::$TTL + 1);

            $poc1 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                                  Poc::PARAM_OUTPUTHANDLER => new TestOutput(),
                                  Poc::PARAM_HASHER => $hasher ));
            $this->pocBurner($poc1, self::TESTSTRING3);
            $output3 = $this->getOutput();

            $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);
            $this->assertEquals(self::TESTSTRING1, $output2, $cacheHandlerName);
            $this->assertEquals(self::TESTSTRING3, $output3, $cacheHandlerName);

            $this->assertNotEquals($output1, $output3, $cacheHandlerName);
            $this->assertEquals($output1, $output2, $cacheHandlerName);

        }
    }

    public function testPocBlacklist ()
    {
        $blackList = new Filter();
        $blackList->addBlacklistCondition(true);

        $hasher = new Hasher();
        $hasher->addDistinguishVariable("testPocBlacklist".  rand());

        $cacheHandler = new FileCache(
                array(CacheParams::PARAM_TTL => PocTest::BIGTTL,
                      ));

        $poc1 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc1, rand());

        $poc2 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc2, '1');

        $poc3 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc3, self::NEEDLE);
        $output1 = $this->getOutput();

        $poc4 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc4, self::TESTSTRING2);
        $output2 = $this->getOutput();

        $this->assertTrue(! empty($output1));
        $this->assertTrue($output1 != $output2);
    }

    public function testPocWithDifferentHashers ()
    {
        $objects = new \Pimple();
        $ttl = self::$TTL;

        $objects['c1'] = function  () use ($ttl) {
            return new FileCache(array(CacheParams::PARAM_TTL => $ttl
                                       ));
        };

        $objects['c2'] = function  () use ($ttl) {
            return new FileCache(array(CacheParams::PARAM_TTL => $ttl));
        };

        $cacheHandler1 = $objects['c1'];

        $hasher1 = new Hasher();
        $hasher1->addDistinguishVariable("a".rand());
        $poc1 = new Poc(array(Poc::PARAM_HASHER => $hasher1,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc1, self::NEEDLE);
        $output1 = $this->getOutput();

        $hasher2 = new Hasher();
        $hasher2->addDistinguishVariable("b".rand());
        $poc2 = new Poc(array(Poc::PARAM_HASHER => $hasher2,
                              Poc::PARAM_OUTPUTHANDLER => new TestOutput() ));
        $this->pocBurner($poc2, self::TESTSTRING2);
        $output2 = $this->getOutput();

        $this->assertTrue($output1 != $output2);
    }

}
