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

\ob_start();

include 'framework/autoload.php';

class testClassTest extends \PHPUnit_Framework_TestCase{

  private function cacheBurner(){
    $testString="\n\ntestString\n\n";
    //$apc = new \ApcCache(new Evaluateable('#php$#', 'tester.php', Evaluateable::OP_PREGMATCH),5);
    $apc = new \FileCache(new Evaluateable('#php$#', 'tester.php', Evaluateable::OP_PREGMATCH),50,'/tmp/');
    $pob = new Pob(new \PobCache($apc), new TestOutput(), true);
    echo $testString;
    unset($pob);
  }

  public function test_01_fill(){

//    for ($i = 0; $i < 1; $i++){
      $this->cacheBurner();
//    }
//    $this->assertFalse(false);
  }

  public function test_02_fill(){
    $this->cacheBurner();
  }
}
?>
