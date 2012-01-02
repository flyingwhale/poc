<?php

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
  function __construct($options,$optionAble){
    $this->options = $options;
    $this->optionAble = $optionAble;
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
    
  /**
 * @return the $options
 */
  public function getOptions() {
    return $this->options;
  }

   /**
   * @return the $indexes
   */
  public function getIndexes() {
    return $this->indexes;
  }
  

  public function setOptions($options){
    $this->options = $options;
  }

  public function getOption($key){
    if(isset($this->options[$key])){
      return $this->options[$key];
    }
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
