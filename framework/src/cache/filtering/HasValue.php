<?php
abstract class HasValue implements ToString{
  
  protected $value;
  
  protected $valueDestription;

  abstract function setValue();

  function setValueDescription($value) {
    $this->valueDestription = $value;
  }

  function getValue() {
    return $this->value;
  }
  
  function getValueDescription() {
    return $this->valueDestription;
  }

  function toString() {
    return serialize($this);
  }

  abstract function selfEvaluate();
}
?>
