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
use POC\Pob;
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

  private function cacheBurner(){
    \ob_start('\unittest\set_output');
    $testString="\n\ntestString\n\n";
    $apc = new \FileCache(new Evaluateable('#php$#', 'tester.php', Evaluateable::OP_PREGMATCH),5,'/tmp/');
    $pob = new Pob(new \PobCache($apc), new TestOutput(), true);
    echo $testString;
    $pob->destruct();
    \ob_end_flush();
    //unset($pob);

    //echo  $GLOBALS['analyzeThisOutput']."HEHEHEHE";
  }

  public function test_01_fill(){
    
//  $this->cacheBurner();
    $this->cacheBurner();
    $output1 = get_output();
    echo($output1.'w');

//  $this->cacheBurner();
    $output2 = get_output();
    echo($output1.'WW');

//  $this->cacheBurner();
    $output3 = get_output();
    echo($output1.'WwW');

//    $output3 = get_output();
echo '1'.$output1.'2'.$output2.'3'.$output3;
//    $this->assertTrue('testString', $output1);
//    $this->assertTrue('testString', $output2);
//    $this->assertTrue('testString', $output3);
    $this->assertTrue($output1 == $output2);
   
    //$this->assertTrue($output2 == $output3);
    //for ($i = 0; $i < 1; $i++){
    //    $this->cacheBurner();
    //}
    //$this->assertFalse(false);
  }
}
?>
