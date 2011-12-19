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

class PocTest extends \PHPUnit_Framework_TestCase{

  private static $analizeThisOutput;
  private static $analizeThisHeader;

  static function  setOutput($o){
    self::$analizeThisOutput = $o;
  }

  static function getOutput(){
    if (isset(self::$analizeThisOutput))
      return self::$analizeThisOutput;
  }

  private function getHeader(){
    return $this->analizeThisHeader;
  }

  private function cacheBurner($testString="\n\ntestString\n\n",
                                                                $cacheHandler) {
    \ob_start(array('\unittest\PocTest','setOutput'));
    $output = new TestOutput();
    $pob =
       new Poc(new \POC\cache\PocCache($cacheHandler), $output, false);
    echo $testString;
    $this->analizeThisHeader = $output->getHeader();
    $pob->destruct();
    \ob_end_flush();
  }


  public function testBasicPocFunctionality(){

    $eval = new Evaluateable('#php$#', 'tester.php',
                                                   Evaluateable::OP_PREGMATCH);
    $handlers = array();
    try{
      $handlers[] = new \FileCache($eval,1,'/tmp/');
      $handlers[] = new \MemcachedCache($eval, 1, 'localhost');
//      $handlers[] = new \RediskaCache($eval, 1, 'localhost');
      
      foreach($handlers as $cacheHandler) {
        $this->cacheBurner("1",$cacheHandler);
        sleep(2);

        $this->cacheBurner("\ntest1\n",$cacheHandler);
        $output1 = $this->getOutput();
        $this->assertTrue(!is_array($this->analizeThisHeader));

        for ($i = 0; $i < 2; $i++){
          $this->cacheBurner($i,$cacheHandler);
        }

        $this->cacheBurner("\ntest2\n",$cacheHandler);
        $output2 = $this->getOutput();
        sleep(2);

        $this->cacheBurner("\ntest3\n",$cacheHandler);
        $output3 = $this->getOutput();
        $l = new \Logger();

        $l->lwrite( '1'.$output1.'2'.$output2.'3'.$output3 );
        $this->assertTrue($output1 == $output2);
        $this->assertTrue($output1 != $output3);
      }
    } catch (Exception $e) {
      $this->assertTrue(false);
    }
  }
}
