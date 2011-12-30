<?php

namespace POC\core;

abstract class OptionAble extends \Pimple implements OptionAbleInterface
{
  private $options = array();
  private $indexes = array();

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
}
