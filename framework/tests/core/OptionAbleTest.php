<?php
/*Copyright 2012 Imre Toth <tothimre at gmail>

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

use POC\Poc;

use POC\core\OptionAble;

use POC\core\OptionAbleInterface;

class MockOptionAbleInterfaceClass implements OptionAbleInterface
{
  /**
   * 
   * @var OptionAble
   */
  public $optionAble = null;
  
  function __construct($options = array()){
    $this->optionAble = new OptionAble($options, $this);
    $this->optionAble->start();
  }
  
  function fillDefaults(){
    $this->optionAble['a'] = 'A';
    $this->optionAble['poc'] = new Poc();
  }
  
}

/**
 * test case.
 */
class OptionAbleTest extends \PHPUnit_Framework_TestCase 
{
 
  function testStart()
  {
    
    $test = new MockOptionAbleInterfaceClass();;

    $this->assertTrue($test->optionAble->getOption('a') == 'A');
    $this->assertTrue(get_class($test->optionAble->getOption('poc')) == 'POC\Poc' );
     
    $test = new MockOptionAbleInterfaceClass(array('a'=>'b'));
    $this->assertTrue($test->optionAble->getOption('a') == 'b');
    $exception = false;
    try{
      $test = new MockOptionAbleInterfaceClass('a');
    }
    catch (\Exception $e){
      $exception = true;
    }
    if(!$exception){
      $this->fail('An expected exception has not been raised.');
    }
  }
  
}

