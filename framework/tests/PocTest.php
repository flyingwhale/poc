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

//\ob_start('\unittest\hide_output');
\ob_start();

function hide_output($o){  
}

function set_output($o){
  $GLOBALS['analyzeThisOutput'] = $o;
}

function get_output(){
  $tmp = $GLOBALS['analyzeThisOutput'];
  return $tmp;
}

include 'framework/autoload.php';

class TestClassTest extends \PHPUnit_Framework_TestCase{
  
  private $analyzeThisOutput;

  static function  setAnalyzeThisOutput($o){
    $this->analyzeThisOutput = $o;  
  }

  private function cacheBurner($testString="\n\ntestString\n\n"){
    \ob_start('\unittest\set_output');
    $apc = new \FileCache(new Evaluateable('#php$#', 'tester.php', 
                                         Evaluateable::OP_PREGMATCH),1,'/tmp/');
    $pob = new Poc(new \PocCache($apc), new TestOutput(), false);
    echo $testString;
    $pob->destruct();
    \ob_end_flush();
  }

  public function test_01_fill(){
    
//  $this->cacheBurner();
    $this->cacheBurner("\ntest1\n");
    $output1 = get_output();

    $this->cacheBurner("\ntest1\n");
    $output2 = get_output();

    sleep(2);

    $this->cacheBurner("\ntest3\n");
    $output3 = get_output();

$l = new \Logger();
//    $output3 = get_output();
$l->lwrite( '1'.$output1.'2'.$output2.'3'.$output3 );
    $this->assertTrue($output1 == $output2);
    $this->assertTrue($output1 != $output3);
   
    //$this->assertTrue($output2 == $output3);
    //for ($i = 0; $i < 1; $i++){
    //    $this->cacheBurner();
    //}
    //$this->assertFalse(false);
  }
}
?>
