<?php

abstract class Resource extends Evaluatable {

  const EQUALATION = 1;
  const PREGMATCH = 2;


  var $value;
  var $pattern;
  var $operation;
  var $key;

  function __construct ($pattern, $operation=self::EQUALATION) {
    $this->pattern = $pattern;
    $this->value = $this->setValue();
    $this->opertation = $operation;
    $this->setKey();
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
     return preg_match($this->pattern, $this->value);
    }

  }

  function getKey() {
    //echo $this->key;
    return $this->key;
  }
  function setKey() {
    $vars = get_object_vars($this);
    $str='';
    foreach ($vars as $name=>$val) {
      $str .= $name.$val; 
    }
    $this->key = md5($str);
  }
  
}
