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
use POC\cache\filtering\OutputFilter;

use framework\src\cache\cacheimplementation\CacheParams;

use POC\PocParams;

use POC\Pocparameters;

use POC\cache\cacheimplementation\AbstractPocCacheSpecific;

use POC\cache\header\HeaderManipulator;

use POC\cache\filtering\Evaluateable;
use unittest\handler\TestOutput;
use POC\Poc;
use POc\cache\PocCache;
use POC\cache\cacheimplementation\FileCache;
use POC\cache\cacheimplementation\MemcachedCache;
use POC\cache\cacheimplementation\RediskaCache;
use POC\cache\cacheimplementation\MongoCache;
use POC\cache\filtering\Hasher;
use POC\cache\filtering\Filter;
use POC\cache\tagging\MysqlTagging;

require_once 'framework/autoload.php';

const UNITTESTING = 1;

/* ob_start at the beginning has to be done in order to avoid the headers to be sent, because the
   PHPUnit already has got some output when it starts. */
\ob_start();


//include_once '../autoload.php';
include_once './framework/autoload.php';

class PocTest extends \PHPUnit_Framework_TestCase
{
  const TESTSTRING1 = 1;
  const TESTSTRING2 = 2;
  const TESTSTRING3 = 3;

  const TTL = 1;

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

  /**
   * 
   * @param Cache $cache
   * @param string outputHandlertring
   */
  private function cacheBurner($cache, $testString = "testString") {
  	$outputHandler = new TestOutput();
  	$poc = new Poc(array(PocParams::PARAM_CACHE => $cache, PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
    $this->pocBurner($poc, $outputHandler, $testString);
  }
  
  /**
   * 
   * @param Poc $poc
   * @param string $testString
   */
  private function pocBurner(Poc $poc,$outoutHandler, $testString = "testString") {
  	$this->setOutput('');
  	$poc->start();
  
  	if($outoutHandler->getOutputFlow()){
  		echo $testString;
  		$poc->destruct();
  		$this->setHeader($outoutHandler->getHeader());
  		$this->setOutput($outoutHandler->getOutput());
  	} else {
  		$this->setHeader($outoutHandler->getHeader());
  		$this->setOutput($outoutHandler->getOutput());
  		$poc->destruct();
  	}
  }
  

  
  public function testBasicPocFunctionality(){
    try{
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
        return new MongoCache(array(CacheParams::PARAM_TTL=>PocTest::TTL));
      };
            
      $handlers[] = 'file';
      $handlers[] = 'memcached';
      $handlers[] = 'rediska';
      $handlers[] = 'mongo';
      
      foreach($handlers as $cacheHandlerName) {
        $cacheHandler = $objects[$cacheHandlerName];
        $this->cacheBurner($cacheHandler,"1");
        sleep(self::TTL + 1);

        $this->cacheBurner($cacheHandler,self::TESTSTRING1);
        $output1 = $this->getOutput();
        $this->assertTrue(!is_array($this->getHeader()));

        for ($i = 0; $i < 2; $i++){
          $this->cacheBurner($cacheHandler,self::TESTSTRING1.'Whatever');
        }

        $this->cacheBurner($cacheHandler,self::TESTSTRING2);
        $output2 = $this->getOutput();
        sleep(self::TTL + 1);

        $this->cacheBurner($cacheHandler,self::TESTSTRING3);
        $output3 = $this->getOutput();

        echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";

        $this->assertTrue($output1 == $output2);
        $this->assertTrue($output1 != $output3);
      }
    } catch (\Exception $e) {
      $this->assertTrue(false);
    }
  }

  public function testPocBlacklist(){
  	try{
    		
  		$blackList = new Filter();
  		$blackList->addBlacklistCondition(true);

        $cacheHandler = new FileCache(array(CacheParams::PARAM_TTL=>PocTest::TTL*100, 
       		                                 CacheParams::PARAM_FILTER => $blackList));
        
        $this->cacheBurner($cacheHandler,"1");
        
        $this->cacheBurner($cacheHandler,self::TESTSTRING1);
        $output1 = $this->getOutput();  
  
        $this->cacheBurner($cacheHandler,self::TESTSTRING2);
        $output2 = $this->getOutput();
  
        echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";
  
        $this->assertTrue($output1 != $output2);

  	} catch (\Exception $e) {
  		$this->assertTrue(false);
  	}
  }

  /*
  public function testPocWithDifferentHashers(){
  	$handlers = array();
  	try{
  
  		$objects = new \Pimple();
  
  		$objects['c1'] = function(){
  			$hasher->addDistinguishVariable("dist1");
  		  return new MemcachedCache(array(CacheParams::PARAM_TTL=>2*PocTest::TTL));
  		};
  
  		$objects['c2'] = function(){
  			$hasher = new Hasher();
  			$hasher->addDistinguishVariable("dist2");
  			return new MemcachedCache(array(CacheParams::PARAM_TTL=>2*PocTest::TTL,
  					                        CacheParams::PARAM_HASHER=>$hasher));
  		};
  
  
  		$cacheHandler1 = $objects['c1'];
  		
  		$this->cacheBurner($cacheHandler1,"1");
  		sleep(2*self::TTL + 1);
  
  		$this->cacheBurner($cacheHandler1, self::TESTSTRING1);
  		$output1 = $this->getOutput();
  		$this->assertTrue(!is_array($this->getHeader()));
  
  		$cacheHandler2 = $objects['c2'];
  		$this->cacheBurner($cacheHandler2,self::TESTSTRING2);
  		$output2 = $this->getOutput();
  		echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";
  
  		$this->assertTrue($output1 != $output2);
  	} catch (Exception $e) {
  		$this->assertTrue(false);
  	}
  }
*/
  /*
  public function testPocrs(){
  	$handlers = array();
  	try{
  
  		$objects = new \Pimple();
  		$outputHandler = new TestOutput();
  		
  		$cache = new MemcachedCache(array(CacheParams::PARAM_TTL=>PocTest::TTL*100,
  				CacheParams::PARAM_FILTER => $blackList));
  		
  
  		$objects['p1'] = function(){
  		  return new Poc(array(PocParams::PARAM_CACHE => $cache, 
  		                       PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
  		};
  		
  		$objects['p2'] = function(){
  		  $outputFilter = new OutputFilter();
  		  $outputFilter->addBlacklistCondition(PocTest::NEEDLE);
  		  return new Poc(array(PocParams::PARAM_CACHE => $cache,
  					           PocParams::PARAM_OUTPUTHANDLER => $outputHandler,
  			                   PocParams::PARAM_OUTPUTFILTER => $outputFilter));
  			//  		  $this->pocBurner($poc, $outputHandler, $testString);
  		};
  		
  
  		$poc2 = $objects['p2'];
  		$this->pocBurner($poc, $outputHandler, rand().PocTest::NEEDLE.rand());
  		$output1 = $this->getOutput();
  		
  		$poc1 = $objects['p2'];
  		$this->pocBurner($poc, $outputHandler, rand().PocTest::NEEDLE.rand());
  		$output2 = $this->getOutput();

  		$l = new \Logger(); $l->lwrite($output1);
  		echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";
  		
  		$this->assertTrue($output1 != $output2);
  		
  	} catch (\Exception $e) {
  		$this->assertTrue(false);
  	}
  }
  
  
  
  
  /*
   * 
   	$outputHandler = new TestOutput();
  	$poc = new Poc(array(PocParams::PARAM_CACHE => $cache, PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
    $this->pocBurner($poc, $outputHandler, $testString);

   * */
  
}

