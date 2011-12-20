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
use unittest\handler\TestOutput;
use POC\Poc;

const UNITTESTING = 1;

/* ob_start at the beginning has to be done in order to avoid the headers to be sent, because the
   PHPUnit already has got some output when it starts. */
\ob_start();

include 'framework/autoload.php';

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

  private function cacheBurner($testString="\n\ntestString\n\n",
                                                                $cacheHandler) {

    $this->setOutput('');
    $output = new TestOutput();
    $pob = new Poc(new \POC\cache\PocCache($cacheHandler), $output, false);
    if($output->getOutputFlow()){
      echo $testString;
      $pob->destruct();
      $this->setHeader($output->getHeader());
      $this->setOutput($output->getOutput());
    } else {
     $this->setHeader($output->getHeader());
     $this->setOutput($output->getOutput());
     $pob->destruct();
    }

  }


  public function testBasicPocFunctionality(){
    $eval = new Evaluateable('#php$#', 'tester.php',
                                                   Evaluateable::OP_PREGMATCH);
    $handlers = array();
    try{
      $handlers[] = new \FileCache($eval, self::TTL ,'/tmp/');
      $handlers[] = new \MemcachedCache($eval, self::TTL, 'localhost');
      $handlers[] = new \RediskaCache($eval, self::TTL, array('servers' =>
                         array(array('host' => 'localhost', 'port' => 6379))));

      foreach($handlers as $cacheHandler) {
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

        $this->assertTrue($output1 == $output2);
        $this->assertTrue($output1 != $output3);
      }
    } catch (Exception $e) {
      $this->assertTrue(false);
    }
  }
}
