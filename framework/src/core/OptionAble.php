<?php

namespace POC\core;

abstract class OptionAble extends \Pimple implements OptionAbleInterface
{
  private $options = array();
  private $indexes = array();

  function __construct($options){
    $this->options = $options;  
    $this->fillDefaults();
    
    if(is_array($options)){
    //if(1){
     $this->optionsMerge();
    } else {
      throw new \Exception('Please add an array or nothing to the
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
