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

use Poc\Plugins\MinifyHtmlOutput;

use Poc\Plugins\PocLogsParams;

use Poc\Plugins\PocLogs;

use Poc\Plugins\TestPlugin\TestPlugin;

use Poc\Cache\CacheInvalidationProtection\CIAProtector;
use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
use Poc\Pocparameters;
use Poc\Cache\Cache\CacheImplementationtation\AbstractPocCacheSpecific;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Cache\Filtering\Evaluateable;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\PocCache;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\RediskaCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Cache\Tagging\MysqlTagging;


const UNITTESTING = 1;

/* ob_start at the beginning has to be done in order to avoid the headers to be sent, because the
   PHPUnit already has got some output when it starts. */
\ob_start();


class PocTest extends \PHPUnit_Framework_TestCase
{
  const TESTSTRING1 = 1;
  const TESTSTRING2 = 2;
  const TESTSTRING3 = 3;

  const TTL = 1;
  const BIGTTL = 100;

  const NEEDLE = '/amiga1200/';

  private $analizeThisOutput;
  private $analizeThisHeader;

  private function setOutput($o) {
    $this->analizeThisOutput = $o;
  }

  private function getOutput() {
    return $this->analizeThisOutput;
  }

  private function getHeader() {
    return $this->analizeThisHeader;
  }

  private function setHeader($header) {
    $this->analizeThisHeader = $header;
  }
/*
  public function __construct(){
    new PocLogs();  
  }*/
  
  /**
   *
   * @param Cache $cache
   * @param string $testString
   */
  private function cacheBurner($cache, $testString = "testString") {
    $outputHandler = new TestOutput();
  	$poc = new Poc(array(PocParams::PARAM_CACHE => $cache, PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
    $this->pocBurner($poc, $outputHandler, $testString);
  }

  /**
   *
   * @param Poc $poc
   * @param TestOutput $outputHandler
   * @param string $testString
   */
  private function pocBurner(Poc $poc,$outputHandler, $testString = "testString") {
    $pl = new PocLogs(array(PocLogsParams::PARAM_EVENT_DISPTCHER => $poc->getPocDispatcher()));
    //new MinifyHtmlOutput($poc->getPocDispatcher());
    
    $this->setOutput('');
  	$poc->start();
  
  	if($outputHandler->getOutputFlow()){
  		echo $testString;
  		$poc->destruct();
  		$pl->__destruct();
  		$this->setHeader($outputHandler->getHeader());
  		$this->setOutput($outputHandler->getOutput());  		
  	} else {
  	    $this->setHeader($outputHandler->getHeader());
        $this->setOutput($outputHandler->getOutput());
        $poc->destruct();
        $pl->__destruct();
        
        if($outputHandler->getOutput()){
          $this->setHeader($outputHandler->getHeader());
          $this->setOutput($outputHandler->getOutput());
        }
  	}
  }


  public function testBasicPocFunctionality(){
    $objects = new \Pimple();

    $objects['file'] = function()
    {
      return new FileCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
    };

    $objects['memcached'] = function(){
      return new MemcachedCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
    };

    $objects['rediska'] = function(){
      return new RediskaCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
    };

    $objects['mongo'] = function(){
      return new MongoDBCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
    };

    $handlers[] = 'file';
    $handlers[] = 'memcached';
    $handlers[] = 'rediska';
    $handlers[] = 'mongo';

    foreach($handlers as $cacheHandlerName) {
      $cacheHandler = $objects[$cacheHandlerName];

      $this->cacheBurner($cacheHandler,self::TESTSTRING1);
      $output1 = $this->getOutput();
      // This is because of the Rediska cache implementation,
      // Because it transforms any serialized array to array when it stores it,
      // the result that you will fetch from the cache is an array instead of string.
      // To eliminate this behaviour some exta line has to be added to that
      // class
      $this->assertTrue(!is_array($this->getHeader()));

      for ($i = 0; $i < 2; $i++){
        $this->cacheBurner($cacheHandler,self::TESTSTRING1.'Whatever');
      }

      $this->cacheBurner($cacheHandler,self::TESTSTRING2);
      $output2 = $this->getOutput();
      sleep(self::TTL + 1);

      $this->cacheBurner($cacheHandler,self::TESTSTRING3);
      $output3 = $this->getOutput();

      $this->assertTrue($output1 == $output2);
      $this->assertTrue($output1 != $output3);
    }
  }

  public function testPocBlacklist(){
    $blackList = new Filter();
    $blackList->addBlacklistCondition(true);

    $cacheHandler = new FileCache(array(CacheParams::PARAM_TTL=>PocTest::BIGTTL,
   		                                 CacheParams::PARAM_FILTER => $blackList));

    $this->cacheBurner($cacheHandler,"1");

    $this->cacheBurner($cacheHandler,self::NEEDLE);
    $output1 = $this->getOutput();
    
    $this->cacheBurner($cacheHandler,self::TESTSTRING2);
    $output2 = $this->getOutput();
    $this->assertTrue(!empty($output1));
    $this->assertTrue($output1 != $output2);
  }


  public function testPocWithDifferentHashers(){
    $objects = new \Pimple();

    $objects['c1'] = function(){
      return new FileCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
    };

    $objects['c2'] = function(){
    	$hasher = new Hasher();
    	$hasher->addDistinguishVariable("dist2");
    	return new FileCache(array(CacheParams::PARAM_TTL=>PocTest::TTL,
    			                   CacheParams::PARAM_HASHER=>$hasher));
    };

    $cacheHandler1 = $objects['c1'];

    $this->cacheBurner($cacheHandler1, self::TESTSTRING1);
    $output1 = $this->getOutput();

    $cacheHandler2 = $objects['c2'];
    $this->cacheBurner($cacheHandler2,self::TESTSTRING2);
    $output2 = $this->getOutput();

    $this->assertTrue($output1 != $output2);
  }

  public function testOutputFilter(){
    $objects = new \Pimple();

    $objects['p1'] = function(){
      $outputHandler = new TestOutput();
      $cache = new MemcachedCache(array(CacheParams::PARAM_TTL=>PocTest::BIGTTL));
      $poc = new Poc(array(PocParams::PARAM_CACHE => $cache,
                           PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
      $return = array();
      $return[] = $outputHandler;
      $return[] = $poc;
      return $return;
    };

    $objects['p2'] = function(){
        $outputHandler = new TestOutput();
      $cache = new MemcachedCache(array(CacheParams::PARAM_TTL=>PocTest::BIGTTL));
      $outputFilter = new OutputFilter();
      $outputFilter->addBlacklistCondition(PocTest::NEEDLE);
      $poc = new Poc(array(PocParams::PARAM_CACHE => $cache,
    			           PocParams::PARAM_OUTPUTHANDLER => $outputHandler,
    	                   PocParams::PARAM_OUTPUTFILTER => $outputFilter));
      $return = array();
      $return[] = $outputHandler;
      $return[] = $poc;
      return $return;
    };

    $poc2 = $objects['p2'];
      $this->pocBurner($poc2[1], $poc2[0], rand().PocTest::NEEDLE.rand());
      $output1 = $this->getOutput();

    $poc1 = $objects['p1'];
    $this->pocBurner($poc1[1], $poc1[0], rand());
    $output2 = $this->getOutput();

    echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";

    $this->assertTrue($output1 != $output2);
  }

  function testTagging(){
    $getCache = function($hasher){return new FileCache(array(CacheParams::PARAM_TTL=>PocTest::BIGTTL,
    		CacheParams::PARAM_HASHER=>$hasher, CacheParams::PARAM_TAGDB=>new MysqlTagging()));};
    

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

    $cache1->addCacheInvalidationTags(true,'stuff');

    $this->cacheBurner($cache1,"1");
    $o1 = $this->getOutput();
    $this->cacheBurner($cache2,"2");
    $o2 = $this->getOutput();
    $this->cacheBurner($cache3,"3");
    $o3 = $this->getOutput();


    $this->assertTrue($o1 == $o2);
    $this->assertTrue($o1 == $o3);

    $cache1->addCacheInvalidationTags(true,'user');
    
    $this->cacheBurner($cache1,"1");
    $o1 = $this->getOutput();
    $this->cacheBurner($cache2,"2");
    $o2 = $this->getOutput();
    $this->cacheBurner($cache3,"3");
    $o3 = $this->getOutput();

    $this->assertTrue($o1 != $o3);
    $this->assertTrue($o2 == $o3);

    $cache1->addCacheInvalidationTags(true,'customer');


    $this->cacheBurner($cache1,"4");
    $o1 = $this->getOutput();
    $this->cacheBurner($cache2,"4");
    $o2 = $this->getOutput();
    $this->cacheBurner($cache3,"4");
    $o3 = $this->getOutput();
    
    $this->assertTrue($o1 == $o3);
    $this->assertTrue($o2 != $o3);
    
  }

  
  function testHeaderMainpulation(){

    $outputHandler = new TestOutput();
    $headerMainpultion = new HeaderManipulator();
    $blackList = new Filter();
    $cache = new MemcachedCache(array(MemcachedCache::PARAM_TTL=>PocTest::BIGTTL,
    		                          MemcachedCache::PARAM_FILTER=>$blackList
        ));
    
    $cache->clearAll();
    $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
    		Poc::PARAM_OUTPUTHANDLER => $outputHandler,
    		Poc::PARAM_DEBUG => true,
            Poc::PARAM_HEADERMANIPULATOR=>$headerMainpultion
        ));
    
    $this->pocBurner($poc, $outputHandler, rand().rand());
    $output1 = $this->getOutput();

    $hl = \headers_list();
  }

  function testCIAProtection(){
  	$outputHandler = new TestOutput();
  	$cache = new FileCache();
  	$cia = new CIAProtector();
    $cia->setCache($cache);
    $snt = $cia->getSentinel();
    $cia->setSentinel(10);
    $snt1 = $cia->getSentinel();
    
    $this->assertTrue($snt == 0);
    $this->assertTrue($snt1 == 10);
    
    $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
    		Poc::PARAM_OUTPUTHANDLER => $outputHandler,));
    $poc->start();

    $this->pocBurner($poc, $outputHandler, rand().rand());

    $this->assertTrue($this->getOutput() == $cia->getRefreshPage());

  }
}

