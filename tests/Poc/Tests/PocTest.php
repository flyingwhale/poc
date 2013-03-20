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

use Poc\Tests\NativeOutputHandlersTestCore;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\PredisCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class PocTest extends \PHPUnit_Framework_TestCase
{

//\extends NativeOutputHandlersTestCore {

    const CACHE_MEMCACHED = 'mc';
    const CACHE_FILE = 'fl';
    const CACHE_RESIDH = 'rd';
    const CACHE_MONGO = 'mn';
    
    const TESTSTRING1 = "1";
    const TESTSTRING2 = "2";
    const TESTSTRING3 = "3";
    const BIGTTL = 100;
    const NEEDLE = '/amiga1200/';

    public static $TTL;
    public static $caches;
    public static $handlers;
    public static $rand;

    public static function setUpBeforeClass() {
        
        self::$TTL = $GLOBALS['TTL'];
        
        self::$rand = rand();
        
        self::$caches = new \Pimple();
        
        self::$caches['ttl'] = $GLOBALS['TTL'];
        
        self::$caches[self::CACHE_FILE] = function ($c) {
                    return new FileCache(array(CacheParams::PARAM_TTL => $c['ttl']));
                };

        self::$caches[self::CACHE_MEMCACHED] = function ($c) {
                    return new MemcachedCache(array(CacheParams::PARAM_TTL => $c['ttl']));
                };

        self::$caches[self::CACHE_RESIDH] = function ($c){
                    return new PredisCache(array(CacheParams::PARAM_TTL => $c['ttl']));
                };

        self::$caches[self::CACHE_MONGO] = function ($c){
                    return new MongoDBCache(array(CacheParams::PARAM_TTL => $c['ttl']));
                };

        self::$handlers[] = self::CACHE_FILE;
        self::$handlers[] = self::CACHE_MEMCACHED;
        self::$handlers[] = self::CACHE_RESIDH;
        self::$handlers[] = self::CACHE_MONGO;

    }


    public function testBasicPocFunctionalityBigTTL() {
        
        self::$caches['ttl'] = 100;
        
        foreach (self::$handlers as $cacheHandlerName) {
            $testAdapter = new NativeOutputHandlersTestCore;
            $cacheHandler = self::$caches[$cacheHandlerName];
            $hasher = new Hasher();
            $hasher->addDistinguishVariable($cacheHandlerName . self::$rand);

            $poc1 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_HASHER => $hasher));

            $testAdapter->pocBurner($poc1, self::TESTSTRING1);

            $output1 = $testAdapter->getOutput();
            $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);
 
        }
    }

    /**
     * @depends testBasicPocFunctionalityBigTTL
     */
    public function testBasicPocFunctionalityGetCacheWithBigTTL() {
        
        self::$caches['ttl'] = 100;
        
        foreach (self::$handlers as $cacheHandlerName) 
        {
            $testAdapter = new NativeOutputHandlersTestCore;
            $cacheHandler = self::$caches[$cacheHandlerName];
            $hasher = new Hasher();
            $hasher->addDistinguishVariable($cacheHandlerName . self::$rand);

            $poc1 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_HASHER => $hasher));

            $testAdapter->pocBurner($poc1, self::TESTSTRING1."aaa");

            $output1 = $testAdapter->getOutput();
            $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);
 
        }
    }

     /**
     * @depends testBasicPocFunctionalityGetCacheWithBigTTL
     */
    public function testBasicPocFunctionality() {

        self::$caches['ttl'] = $GLOBALS['TTL'];

        foreach (self::$handlers as $cacheHandlerName) {
        $testAdapter = new NativeOutputHandlersTestCore;

            $cacheHandler = self::$caches[$cacheHandlerName];

            $hasher = new Hasher();
            $hasher->addDistinguishVariable($cacheHandlerName . rand());

            $poc1 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_HASHER => $hasher));

            $testAdapter->pocBurner($poc1, self::TESTSTRING1);

            $output1 = $testAdapter->getOutput();

            for ($i = 0; $i < 1; $i++) {
                $testAdapter = new NativeOutputHandlersTestCore;
                $poc2 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                            Poc::PARAM_CACHE => self::$caches[$cacheHandlerName],
                            Poc::PARAM_HASHER => $hasher
                        ));
                $testAdapter->pocBurner($poc2, self::TESTSTRING1 . "Whatever $i");
            }

            
            $poc3 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_HASHER => $hasher));
            
          $testAdapter->pocBurner($poc3, self::TESTSTRING2);
          $output2 = $testAdapter->getOutput();

            sleep(self::$TTL + 1);

            $poc4 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_HASHER => $hasher));
            $testAdapter = new NativeOutputHandlersTestCore;
            $testAdapter->pocBurner($poc4, self::TESTSTRING3);
            $output3 = $testAdapter->getOutput();

            $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);
            $this->assertEquals(self::TESTSTRING1, $output2, $cacheHandlerName);
            $this->assertEquals(self::TESTSTRING3, $output3, $cacheHandlerName);

            $this->assertNotEquals($output1, $output3, $cacheHandlerName);
            $this->assertEquals($output1, $output2, $cacheHandlerName);
 
        }
    }

    public function testPocBlacklist() {
        $testAdapter = (new NativeOutputHandlersTestCore);

        $blackList = new Filter();
        $blackList->addBlacklistCondition(false);
        $blackList->addBlacklistCondition(true);
        $blackList->addBlacklistCondition(false);

        $hasher = new Hasher();
        $hasher->addDistinguishVariable("testPocBlacklist" . rand());

        $poc1 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    ));
        $testAdapter->pocBurner($poc1, rand());

        $poc2 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    ));
        $testAdapter->pocBurner($poc2, '1');

        $poc3 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    ));
        $testAdapter->pocBurner($poc3, self::NEEDLE);
        $output1 = $testAdapter->getOutput();

        $poc4 = new Poc(array(Poc::PARAM_FILTER => $blackList,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    ));
        $testAdapter->pocBurner($poc4, self::TESTSTRING2);
        $output2 = $testAdapter->getOutput();

        $this->assertTrue(!empty($output1));
        $this->assertTrue($output1 != $output2);
    }

    public function testPocWithDifferentHashers() {
        $testAdapter = (new NativeOutputHandlersTestCore);

        $objects = new \Pimple();
        $ttl = self::$TTL;

        $objects['c1'] = function () use ($ttl) {
                    return new FileCache(array(CacheParams::PARAM_TTL => $ttl
                            ));
                };

        $objects['c2'] = function () use ($ttl) {
                    return new FileCache(array(CacheParams::PARAM_TTL => $ttl));
                };

        $cacheHandler1 = $objects['c1'];

        $hasher1 = new Hasher();
        $hasher1->addDistinguishVariable("a" . rand());
        $poc1 = new Poc(array(Poc::PARAM_HASHER => $hasher1,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_OUTPUTHANDLER => new TestOutput()));
        $testAdapter->pocBurner($poc1, self::NEEDLE);
        $output1 = $testAdapter->getOutput();

        $hasher2 = new Hasher();
        $hasher2->addDistinguishVariable("b" . rand());
        $poc2 = new Poc(array(Poc::PARAM_HASHER => $hasher2,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_OUTPUTHANDLER => new TestOutput()));
        $testAdapter->pocBurner($poc2, self::TESTSTRING2);
        $output2 = $testAdapter->getOutput();

        $this->assertTrue($output1 != $output2);
    }

}
