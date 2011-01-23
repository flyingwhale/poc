<?php
abstract class HasValue {
  
  protected $value;
  
  protected $valueDestription;

  abstract function setValue();

  abstract function setValueDescription($value);

  function getValue() {
    return $this->value;
  }
  function getValueDescription() {
    return $this->valueDestription;
  }
    
}
?>
