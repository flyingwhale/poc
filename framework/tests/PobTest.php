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
const UNITTESTING = 1;

include 'framework/autoload.php';

class testClassTest extends \PHPUnit_Framework_TestCase{

  private function cacheBurn(){
    $testString="\ntest test test test test test test test test test test test test test test test";
//  $apc = new \ApcCache(new Evaluateable('aaaa', 'aaaa', Evaluateable::OP_EQUALATION),5);
    $apc = new \ApcCache(new Evaluateable('#php$#', 'tester.php', Evaluateable::OP_PREGMATCH),5);

    $pob = new \Pob(new \PobCache($apc), new TestOutput(), true);
    echo $testString;
    unset($pob);
  }

  public function testStart01(){

    for ($i = 0; $i < 2; $i++){
      $this->cacheBurn();
    }
    $this->assertFalse(false);
    unset($pob);
  }
}
?>
