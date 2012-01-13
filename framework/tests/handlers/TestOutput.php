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

use POC\core\OptionAble;

use POC\core\OptionAbleInterface;

use POC\Handlers\OutputInterface;

use POC\cache\header\HeaderManipulator;

use POC\Poc;

class TestOutput implements OutputInterface, OptionAbleInterface
{
  const HEADER_BASIC = 'a:1:{i:0;s:35:"X-Powered-By: PHP/5.3.6-13ubuntu3.3";}';
  
  const PARAM_HEADER = 'header';
  
  /**
   * Stores the serialized arrays of "fake" headers for testing reasons.
   * 
   * @var string
   */
  private $actHeader;

  /**
   * 
   * @var HeaderManipulator
   */
  private $header = null;
  
  private $outputFlow = 1;
  private $output = '';
  private $optionable;
  
  function fillDefaults(){
    $this->optionable[self::PARAM_HEADER] = self::HEADER_BASIC;  
  }
  
  function __construct($options = array())
  {
    $this->optionable = new OptionAble($options, $this);
    $this->optionable->start();
    $this->actHeader = $this->optionable->getOption(self::PARAM_HEADER);
  }
  
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
  
  /**
   * 
   * @return array The actual fake header list;
   */
  function headersList(){
  	return \unserialize($this->actHeader);
  }
  
}

