<?php

abstract class Resource implements Evaluatable {

  const EQUALATION = 1;
  const PREGMATCH = 2;
  const BEGINS = 3;
  const ISNULL = 3;
  const NOTNULL = 3;

  var $value;
  var $pattern;
  var $operation;

  function __construct ($pattern, $operation=self::EQUALATION) {
    $this->pattern = $pattern;
    $this->value = $this->setValue();
    $this->opertation = $operation;
  }

  function getValue() {
    return $this->value;
  }

  abstract function setValue();

  function evaluate() {
    if($this->opertation == self::EQUALATION) {
      return ($this->pattern == $this->value);
    }
    if($this->opertation == self::PREGMATCH) {
      //TODO: implement
    }
    if($this->opertation == self::BEGINS) {
      //TODO: implement
    }
    if($this->opertation == self::ISNULL) {
      //TODO: implement
    }
    if($this->opertation == self::NOTNULL) {
      //TODO: implement      
    }
    
  }

  function getKey() {
    return md5(var_export($this,true));
  }

}
