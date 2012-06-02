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

use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\RediskaCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;

class PocTest extends PocTestCore
{

    public function testBasicPocFunctionality ()
    {
        $objects = new \Pimple();
        $ttl = self::$TTL;

        $objects['file'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality file".  rand());

            return new FileCache(array(CacheParams::PARAM_TTL => $ttl,
                                       CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['memcached'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality memcached".  rand());

            return new MemcachedCache(
                    array(CacheParams::PARAM_TTL => $ttl,
                          CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['rediska'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality rediska".  rand());

            return new RediskaCache(array(CacheParams::PARAM_TTL => $ttl,
                                          CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['mongo'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality mongo".  rand());

            return new MongoDBCache(array(CacheParams::PARAM_TTL => $ttl,
                                          CacheParams::PARAM_HASHER => $hasher));
        };

        $handlers[] = 'file';
        $handlers[] = 'memcached';
        $handlers[] = 'rediska';
        $handlers[] = 'mongo';

        foreach ($handlers as $cacheHandlerName) {
            $cacheHandler = $objects[$cacheHandlerName];

            $this->cacheBurner($cacheHandler, self::TESTSTRING1);
            $output1 = $this->getOutput();

            // This is because of the Rediska cache implementation,
            // Because it transforms any serialized array to array when it
            // stores it,
            // the result that you will fetch from the cache is an array instead
            // of string.
            // To eliminate this behaviour some exta line has to be added to
            // that
            // class
            $this->assertTrue(! is_array($this->getHeader()));

            for ($i = 0; $i < 10; $i ++) {
                $this->cacheBurner($cacheHandler, self::TESTSTRING1 . "Whatever $i");
            }

            $this->cacheBurner($cacheHandler, self::TESTSTRING2);
            $output2 = $this->getOutput();
            sleep(self::$TTL + 1);

            $this->cacheBurner($cacheHandler, self::TESTSTRING3);
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
                      CacheParams::PARAM_FILTER => $blackList,
                      CacheParams::PARAM_HASHER => $hasher
                      ));

        $this->cacheBurner($cacheHandler, "1");

        $this->cacheBurner($cacheHandler, self::NEEDLE);
        $output1 = $this->getOutput();

        $this->cacheBurner($cacheHandler, self::TESTSTRING2);
        $output2 = $this->getOutput();
        $this->assertTrue(! empty($output1));
        $this->assertTrue($output1 != $output2);
    }

    public function testPocWithDifferentHashers ()
    {
        $objects = new \Pimple();
        $ttl = self::$TTL;

        $objects['c1'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testPocWithDifferentHashers".  rand());

            return new FileCache(array(CacheParams::PARAM_TTL => $ttl,
                                       CacheParams::PARAM_HASHER => $hasher
                                       ));
        };

        $objects['c2'] = function  () use ($ttl) {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testPocWithDifferentHashers dist2". rand());

            return new FileCache(
                    array(CacheParams::PARAM_TTL => $ttl, CacheParams::PARAM_HASHER => $hasher));
        };

        $cacheHandler1 = $objects['c1'];

        $this->cacheBurner($cacheHandler1, self::TESTSTRING1);
        $output1 = $this->getOutput();

        $cacheHandler2 = $objects['c2'];
        $this->cacheBurner($cacheHandler2, self::TESTSTRING2);
        $output2 = $this->getOutput();

        $this->assertTrue($output1 != $output2);
    }

}
