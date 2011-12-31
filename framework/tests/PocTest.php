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
use POC\cache\cacheimplementation\AbstractPocCacheSpecific;

use POC\cache\filtering\OutputFilter;

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

  private function cacheBurner($testString = "testString", $cache) {
    $this->setOutput('');
    $output = new TestOutput();
    $poc = new Poc(array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $output));
    $poc->start();

    if($output->getOutputFlow()){
      echo $testString;
      $poc->destruct();
      $this->setHeader($output->getHeader());
      $this->setOutput($output->getOutput());
    } else {
     $this->setHeader($output->getHeader());
     $this->setOutput($output->getOutput());
     $poc->destruct();
    }
  }


  public function testBasicPocFunctionality(){

    $handlers = array();
    try{
      
      $objects = new \Pimple();
      
      $objects['file'] = function()
      {
        return new FileCache(array(AbstractPocCacheSpecific::PARAM_TTL=>PocTest::TTL));
      };
      
      $objects['memcached'] = function(){
        return new MemcachedCache(array(AbstractPocCacheSpecific::PARAM_TTL=>PocTest::TTL));
      };
      
      $objects['rediska'] = function(){
        return new RediskaCache(array(AbstractPocCacheSpecific::PARAM_TTL=>PocTest::TTL));
      };
      
      $objects['mongo'] = function(){
        return new MongoCache(array(AbstractPocCacheSpecific::PARAM_TTL=>PocTest::TTL));
      };
      
/*$handlers[] = new FileCache(array(AbstractPocCacheSpecific::PARAM_TTL=>self::TTL));
      //$handlers[] = new MemcachedCache(array(AbstractPocCacheSpecific::PARAM_TTL=>self::TTL));
      //$handlers[] = new RediskaCache(array(AbstractPocCacheSpecific::PARAM_TTL=>self::TTL));
      $handlers[] = new MongoCache(array(AbstractPocCacheSpecific::PARAM_TTL=>self::TTL));
      */
      
      $handlers[] = 'file';
      $handlers[] = 'memcached';
      $handlers[] = 'rediska';
      $handlers[] = 'mongo';
      
      foreach($handlers as $cacheHandlerName) {
        $cacheHandler = $objects[$cacheHandlerName];
        $this->cacheBurner("1",$cacheHandler);
        sleep(self::TTL + 1);

        $this->cacheBurner(self::TESTSTRING1,$cacheHandler);
        $output1 = $this->getOutput();
        $this->assertTrue(!is_array($this->getHeader()));

        for ($i = 0; $i < 2; $i++){
          $this->cacheBurner(self::TESTSTRING1.'Whatever',$cacheHandler);
        }

        $this->cacheBurner(self::TESTSTRING2,$cacheHandler);
        $output2 = $this->getOutput();
        sleep(self::TTL + 1);

        $this->cacheBurner(self::TESTSTRING3,$cacheHandler);
        $output3 = $this->getOutput();

        echo "\n\n".'|'.$output1.'|'.$output2.'|'."\n\n";

        $this->assertTrue($output1 == $output2);
        $this->assertTrue($output1 != $output3);
      }
    } catch (Exception $e) {
      $this->assertTrue(false);
    }
  }
}
