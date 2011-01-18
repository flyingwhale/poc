<?php
abstract class Evaluatable {
  
  var $negation = 0;
    
  abstract function evaluate();
  abstract function setKey();
  function negate() {
    $this->negation = 1;
  }
}
