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

namespace POC\core;


class OptionAble extends \Pimple
{
  private $options = array();
  private $optionAble = null;
  private $indexes = array();

  /**
   * 
   * @param array $options
   * @param OptionAbleInterface $optios
   *
   * @throws \Exception
   */
  function __construct($options,$optionAbleInterface){
    $this->options = $options;
    $this->optionAble = $optionAbleInterface;
  }
  
  public function start(){
    $this->optionAble->fillDefaults();
      if(is_array($this->options)){
      $this->optionsMerge();
    } else {
      throw new \Exception('Please add an array or null to the
          $options parameter');
    }
  }
    
  function offsetSet($id, $value)
  {
    parent::offsetSet($id, $value);
    $this->indexes[] = $id;
  }
    
  public function getOption($key){
    if(isset($this->options[$key])){
     $return = $this->options[$key];
    }
    return $return;
  }
  
  /**
   *
   * @param array $srcArray
   * @param OptionAble $oa
   * @return array
   */
  public function optionsMerge(){
    foreach($this->indexes as $key => $value){
      //var_dump($oa->getIndexes());die();
      if(!isset($this->options[$value])) {
        $this->options[$value] = $this[$value];
      }
    }
  }  
  
}
