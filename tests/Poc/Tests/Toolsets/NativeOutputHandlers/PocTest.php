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

namespace Poc\Tests\Toolsets\NativeOutputHandlers;

use Poc\Tests\Toolsets\NativeOutputHandlers\NativeOutputHandlersTestCore;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\PredisCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Exception\CacheNotReachableException;
use Poc\Exception\DriverNotFoundException;
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
    const NEEDLE = '/amiga1000/amiga500/amiga600/amiga1200/amiga4000/amiga/amiga1000/amiga500/amiga600/amiga1200/amiga4000/amiga/';

    public static $TTL;
    public static $caches;
    public static $handlers;
    public static $rand;

    public static function setUpBeforeClass()
    {
        self::$TTL = $GLOBALS['TTL'];
        self::$rand = rand();
    }

    /**
     * @dataProvider basicPocFunctionalityBigTTLProvider
     */
    public function testBasicPocFunctionalityBigTTL($cacheHandlerName, $cacheHandlerOptions)
    {
        try
        {
            $cacheHandler = new $cacheHandlerName($cacheHandlerOptions);
        }
        catch (DriverNotFoundException $e) {
            $this->markTestSkipped($e->getMessage());
        }
        catch (CacheNotReachableException $e) {
            $this->markTestSkipped($e->getMessage());

        }

        $testAdapter = new NativeOutputHandlersTestCore;

        $hasher = new Hasher();
        $hasher->addDistinguishVariable($cacheHandlerName . self::$rand);

        $poc1 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_CACHE => $cacheHandler,
                    Poc::PARAM_HASHER => $hasher));

        $testAdapter->pocBurner($poc1, self::TESTSTRING1);
        ob_end_flush();

        $output1 = $testAdapter->getOutput();
        $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);

        $testAdapter->pocBurner($poc1, self::TESTSTRING1."aaa");

        $output1 = $testAdapter->getOutput();
        $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);


    }

    /**
     * @dataProvider basicPocFunctionalityProvider
     */
    public function testBasicPocFunctionality($cacheHandlerName, $cacheHandlerOptions)
    {
        try
        {
            $cacheHandler = new $cacheHandlerName($cacheHandlerOptions);
        }
        catch (DriverNotFoundException $e) {
            $this->markTestSkipped($e->getMessage());
        }
        catch (CacheNotReachableException $e) {
            $this->markTestSkipped($e->getMessage());

        }

        $testAdapter = new NativeOutputHandlersTestCore;

        $hasher = new Hasher();
        $hasher->addDistinguishVariable($cacheHandlerName . rand());

        $poc1 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_CACHE => $cacheHandler,
                    Poc::PARAM_HASHER => $hasher));

        $testAdapter->pocBurner($poc1, self::TESTSTRING1);
        ob_end_flush();

        $output1 = $testAdapter->getOutput();

        for ($i = 0; $i < 10; $i++) {
            $testAdapter = new NativeOutputHandlersTestCore;
            $poc2 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                        Poc::PARAM_CACHE => $cacheHandler,
                        Poc::PARAM_HASHER => $hasher
                    ));
            $testAdapter->pocBurner($poc2, self::TESTSTRING1 . "Whatever $i");
            ob_end_flush();

        }

        $poc3 = new Poc(array(Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_CACHE => $cacheHandler,
                    Poc::PARAM_HASHER => $hasher));

        $testAdapter->pocBurner($poc3, self::TESTSTRING2);
        ob_end_flush();
        $output2 = $testAdapter->getOutput();

        sleep(self::$TTL + 1);

        $poc4 = new Poc(array(Poc::PARAM_CACHE => $cacheHandler,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput()),
                    Poc::PARAM_HASHER => $hasher));
        $testAdapter = new NativeOutputHandlersTestCore;
        $testAdapter->pocBurner($poc4, self::TESTSTRING3);
        ob_end_flush();
        $output3 = $testAdapter->getOutput();

        $this->assertEquals(self::TESTSTRING1, $output1, $cacheHandlerName);
        $this->assertEquals(self::TESTSTRING1, $output2, $cacheHandlerName);
        $this->assertEquals(self::TESTSTRING3, $output3, $cacheHandlerName);

        $this->assertNotEquals($output1, $output3, $cacheHandlerName);
        $this->assertEquals($output1, $output2, $cacheHandlerName);
    }

    public function testPocBlacklist()
    {
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

    public function testPocWithDifferentHashers()
    {
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
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput())));
        $testAdapter->pocBurner($poc1, self::NEEDLE);
        $output1 = $testAdapter->getOutput();

        $hasher2 = new Hasher();
        $hasher2->addDistinguishVariable("b" . rand());
        $poc2 = new Poc(array(Poc::PARAM_HASHER => $hasher2,
                    Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput())));
        $testAdapter->pocBurner($poc2, self::TESTSTRING2);
        $output2 = $testAdapter->getOutput();

        $this->assertTrue($output1 != $output2);
    }

    public static function basicPocFunctionalityBigTTLProvider()
    {
        $ttl = 100;
        $caches = self::getCaches($ttl);

        return $caches;
    }

    public static function basicPocFunctionalityProvider()
    {
        $ttl = $GLOBALS['TTL'];
        $caches = self::getCaches($ttl);

        return $caches;
    }

    protected static function getCaches($ttl)
    {
        $caches = array();

        $cache = array(
            'Poc\Cache\CacheImplementation\FileCache',
            array(CacheParams::PARAM_TTL => $ttl)
        );
        $caches[] = $cache;

//        $cache = array(
//            'Poc\Cache\CacheImplementation\MemcachedCache',
//            array(CacheParams::PARAM_TTL => $ttl)
//        );
//
//        $caches[] = $cache;
//        $cache = array(
//            'Poc\Cache\CacheImplementation\MongoDBCache',
//            array(CacheParams::PARAM_TTL => $ttl)
//        );
//        $caches[] = $cache;
//
//        $cache = array(
//            'Poc\Cache\CacheImplementation\PredisCache',
//            array(CacheParams::PARAM_TTL => $ttl)
//        );
//        $caches[] = $cache;
//

        return $caches;
    }
}
