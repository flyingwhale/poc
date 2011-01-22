<?php
abstract class HasValue {
  
  protected  $value;
  
  abstract function setValue();
  
  function getValue() {
    return $this->value;
  }
  
}
?>
