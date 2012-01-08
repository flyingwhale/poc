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

namespace unittest\handler;

use POC\cache\header\HeaderManipulator;

use POC\Poc;

class TestOutput implements \POC\handlers\OutputInterface
{
  /**
   * 
   * @var HeaderManipulator
   */
  private $header = null;
  private $outputFlow = 1;
  private $output = '';
  
  /**
   * 
   * @var Poc
   */
  //private $poc;

/*  function __construct(Poc $poc)
  {
    $this->poc = $poc;
  }*/
  
  function getLevel() {
    return ob_get_level();
  }

  function startBuffer($callbackFunctname) {
    ob_start(array('\POC\Poc',$callbackFunctname));
  }

  function stopBuffer() {
    $this->outputFlow = 0;
    \ob_flush();
  }

  function header($header) {
    $this->header = $header;
  }
  public function getHeader() {
    return $this->header;
  }

  function obEnd(){
    $this->outputFlow = 0;
    \ob_flush();
  }

  function getOutputFlow(){
    return $this->outputFlow;
  }

  function cacheCallback($output){
    $this->output = $output;
  }

  function getOutput(){
    return  $this->output;
  }
}

