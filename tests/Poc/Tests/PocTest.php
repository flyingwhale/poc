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

namespace unittest;

use Poc\PocPlugins\MinifyHtmlOutput;

use Poc\PocPlugins\PocLogsParams;

use Poc\PocPlugins\PocLogs;

use Poc\Cache\CacheInvalidationProtection\CIAProtector;
use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\RediskaCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Cache\Tagging\MysqlTagging;

const UNITTESTING = 1;

class PocTest extends \PHPUnit_Framework_TestCase
{

    const TESTSTRING1 = "1";

    const TESTSTRING2 = "2";

    const TESTSTRING3 = "3";

    const TTL = 3;

    const BIGTTL = 100;

    const NEEDLE = '/amiga1200/';

    private $analizeThisOutput;

    private $analizeThisHeader;

    public static function setUpBeforeClass()
    {
        \ob_start(function($output){return"";});
    }
    
    private function setOutput ($o)
    {
        $this->analizeThisOutput = $o;
    }

    private function getOutput ()
    {
        return $this->analizeThisOutput;
    }

    private function getHeader ()
    {
        return $this->analizeThisHeader;
    }

    private function setHeader ($header)
    {
        $this->analizeThisHeader = $header;
    }
    /*
     * public function __construct(){ new PocLogs(); }
     */

    /**
     *
     * @param $cache Cache
     * @param $testString string
     */
    private function cacheBurner ($cache, $testString = "testString")
    {
        $outputHandler = new TestOutput();
        $poc = new Poc(
                array(PocParams::PARAM_CACHE => $cache, PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
        $this->pocBurner($poc, $outputHandler, $testString);
    }

    /**
     * This function has got a weird name, because it does not do anything else
     * only inspect the getOutputFlow function of the output handler and decides
     * what to do with the $testsring variable it receives. This tries to
     * emulate the behahviour of the server to the $poc object.
     *
     * @param $poc Poc
     * @param $outputHandler TestOutput
     * @param $testString string
     */
    private function pocBurner (Poc $poc, $outputHandler,
            $testString = "testString")
    {
        $pl = new PocLogs(array(PocLogsParams::PARAM_POC => $poc));
        // new MinifyHtmlOutput($poc->getPocDispatcher());

        $this->setOutput('');
        $poc->start();

        if ($outputHandler->getOutputFlow()) {
            echo $testString;
            $poc->destruct();
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
        } else {
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
            $poc->destruct();

            if ($outputHandler->getOutput()) {
                $this->setHeader($outputHandler->getHeader());
                $this->setOutput($outputHandler->getOutput());
            }
        }
    }

    public function testBasicPocFunctionality ()
    {
        $objects = new \Pimple();

        $objects['file'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality file".  rand());

            return new FileCache(array(CacheParams::PARAM_TTL => PocTest::TTL,
                                       CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['memcached'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality memcached".  rand());

            return new MemcachedCache(
                    array(CacheParams::PARAM_TTL => PocTest::TTL,
                          CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['rediska'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality rediska".  rand());

            return new RediskaCache(array(CacheParams::PARAM_TTL => PocTest::TTL,
                                          CacheParams::PARAM_HASHER => $hasher));
        };

        $objects['mongo'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testBasicPocFunctionality mongo".  rand());

            return new MongoDBCache(array(CacheParams::PARAM_TTL => PocTest::TTL,
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
            sleep(self::TTL + 1);

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

        $objects['c1'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testPocWithDifferentHashers".  rand());

            return new FileCache(array(CacheParams::PARAM_TTL => PocTest::TTL,
                                       CacheParams::PARAM_HASHER => $hasher
                                       ));
        };

        $objects['c2'] = function  () {
            $hasher = new Hasher();
            $hasher->addDistinguishVariable("testPocWithDifferentHashers dist2". rand());

            return new FileCache(
                    array(CacheParams::PARAM_TTL => PocTest::TTL, CacheParams::PARAM_HASHER => $hasher));
        };

        $cacheHandler1 = $objects['c1'];

        $this->cacheBurner($cacheHandler1, self::TESTSTRING1);
        $output1 = $this->getOutput();

        $cacheHandler2 = $objects['c2'];
        $this->cacheBurner($cacheHandler2, self::TESTSTRING2);
        $output2 = $this->getOutput();

        $this->assertTrue($output1 != $output2);
    }

    // TODO: make this test work!

    public function testOutputFilter ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("testOutputFilter".  rand());

        $outputHandler = new TestOutput();
        $cache = new FileCache(
                array(CacheParams::PARAM_TTL => PocTest::BIGTTL,
                      CacheParams::PARAM_HASHER => $hasher
                      ));
        $outputFilter = new OutputFilter();
        $outputFilter->addBlacklistCondition(PocTest::NEEDLE);
        $poc = new Poc(
                array(PocParams::PARAM_CACHE => $cache,
                      PocParams::PARAM_OUTPUTHANDLER => $outputHandler,
                      PocParams::PARAM_OUTPUTFILTER => $outputFilter,
                      PocParams::PARAM_DEBUG => true
                      ));
        $poc2 = array();
        $poc2[] = $outputHandler;
        $poc2[] = $poc;

        $this->pocBurner($poc2[1], $poc2[0]," POC -> testOutputFilter - ". rand() . PocTest::NEEDLE . rand());
        $this->assertTrue(strpos($this->getOutput(), 'because') != false);
    }

    public function testTagging ()
    {
        $getCache = function  ($hasher) {
            return new FileCache(
                    array(CacheParams::PARAM_TTL => PocTest::BIGTTL, CacheParams::PARAM_HASHER => $hasher, CacheParams::PARAM_TAGDB => new MysqlTagging()));
        };

        $hasher = new Hasher();
        $hasher->addDistinguishVariable("distn1");
        $cache1 = $getCache($hasher);
        $cache1->addCacheAddTags(true, "user,customer,inventory");

        $cache1->clearAll();

        $hasher = new Hasher();
        $hasher->addDistinguishVariable("distn2");
        $cache2 = $getCache($hasher);

        $cache2->addCacheAddTags(true, "inventory");

        $hasher = new Hasher();
        $hasher->addDistinguishVariable("distn3");
        $cache3 = $getCache($hasher);

        $cache3->addCacheAddTags(true, "inventory");
        $cache3->addCacheAddTags(true, "customer");

        $this->cacheBurner($cache1);
        $this->cacheBurner($cache2);
        $this->cacheBurner($cache3);

        $cache1->addCacheInvalidationTags(true, 'stuff');

        $this->cacheBurner($cache1, "1");
        $o1 = $this->getOutput();
        $this->cacheBurner($cache2, "2");
        $o2 = $this->getOutput();
        $this->cacheBurner($cache3, "3");
        $o3 = $this->getOutput();

        $this->assertTrue($o1 == $o2);
        $this->assertTrue($o1 == $o3);

        $cache1->addCacheInvalidationTags(true, 'user');

        $this->cacheBurner($cache1, "1");
        $o1 = $this->getOutput();
        $this->cacheBurner($cache2, "2");
        $o2 = $this->getOutput();
        $this->cacheBurner($cache3, "3");
        $o3 = $this->getOutput();

        $this->assertTrue($o1 != $o3);
        $this->assertTrue($o2 == $o3);

        $cache1->addCacheInvalidationTags(true, 'customer');

        $this->cacheBurner($cache1, "4");
        $o1 = $this->getOutput();
        $this->cacheBurner($cache2, "4");
        $o2 = $this->getOutput();
        $this->cacheBurner($cache3, "4");
        $o3 = $this->getOutput();

        $this->assertTrue($o1 == $o3);
        $this->assertTrue($o2 != $o3);

    }

    public function testHeaderMainpulation ()
    {

        $outputHandler = new TestOutput();
        $headerMainpultion = new HeaderManipulator();
        $blackList = new Filter();
        $cache = new MemcachedCache(
                array(MemcachedCache::PARAM_TTL => PocTest::BIGTTL, MemcachedCache::PARAM_FILTER => $blackList));

        $cache->clearAll();
        $poc = new Poc(
                array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_DEBUG => true, Poc::PARAM_HEADERMANIPULATOR => $headerMainpultion));

        $this->pocBurner($poc, $outputHandler, rand() . rand());
        $output1 = $this->getOutput();

        $hl =\headers_list();
    }

    public function testCIAProtection ()
    {
        $outputHandler = new TestOutput();
        $cache = new FileCache();
        $cia = new CIAProtector();
        $cia->setCache($cache);
        $cnt = $cia->getSentinel();
        $cia->setSentinel(10);
        $cnt1 = $cia->getSentinel();

        $this->assertTrue($cnt == 0);
        $this->assertTrue($cnt1 == 10);

        $poc = new Poc(
                array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_CIA_PROTECTOR => $cia));
        $poc->start();

        $this->pocBurner($poc, $outputHandler, rand() . rand());

        $this->assertTrue($this->getOutput() == $cia->getRefreshPage());

        /*
         * $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
         * Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_CIA_PROTECTOR
         * => new CIAProtector())); $this->pocBurner($poc, $outputHandler,
         * rand().rand()); $this->assertTrue($this->getOutput() !=
         * $cia->getRefreshPage());
         */
    }
}

