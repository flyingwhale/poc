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

\ob_start();

function hide_output($o){
}

function set_output($o){
  $l = new \Logger();

  $l->lwrite( $o );

  $GLOBALS['analyzeThisOutput'] = $o;
}

function get_output(){
  if (isset($GLOBALS['analyzeThisOutput']))
    return $GLOBALS['analyzeThisOutput'];
}

include 'framework/autoload.php';

class TestClassTest extends \PHPUnit_Framework_TestCase{

  private $analyzeThisOutput;

  static function  setAnalyzeThisOutput($o){
    $this->analyzeThisOutput = $o;
  }

  private function cacheBurner($testString="\n\ntestString\n\n",
                                                                $cacheHandler) {
    \ob_start('\unittest\set_output');
    $pob =
       new Poc(new \POC\cache\PocCache($cacheHandler), new TestOutput(), false);
    echo $testString;
    $pob->destruct();
    \ob_end_flush();
  }


  public function test_01_fill(){

    $eval = new Evaluateable('#php$#', 'tester.php',
                                                   Evaluateable::OP_PREGMATCH);
    $handlers = array();

    $handlers[] = new \FileCache($eval,1,'/tmp/');
    $handlers[] = new \MemcachedCache($eval, 1, 'localhost');
    $handlers[] = new ApcCache($eval, 1);

    foreach($handlers as $cacheHandler) {
      $this->cacheBurner("1",$cacheHandler);

      sleep(2);

      $this->cacheBurner("\ntest1\n",$cacheHandler);
      $output1 = get_output();

      for ($i = 0; $i < 2; $i++){
        $this->cacheBurner($i,$cacheHandler);
      }

      $this->cacheBurner("\ntest2\n",$cacheHandler);
      $output2 = get_output();

      sleep(2);

      $this->cacheBurner("\ntest3\n",$cacheHandler);
      $output3 = get_output();
      $l = new \Logger();

      $l->lwrite( '1'.$output1.'2'.$output2.'3'.$output3 );
      $this->assertTrue($output1 == $output2);
      $this->assertTrue($output1 != $output3);
    }
  }
}

