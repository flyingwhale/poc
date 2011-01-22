<?php
abstract class Resource extends Evaluatable implements SelfEvaluateable {

  var $operation;
  var $key;
  var $variablesArray;
  function __construct ($pattern, $operation=self::EQUALATION) {
    parent::__construct();
    $this->pattern = $pattern;
    $this->value = $this->setValue();
    $this->opertation = $operation;
    $this->setKey();
  }


  function selfEvaluate() {

    if($this->opertation == self::EQUALATION) {
      return ($this->pattern == $this->value);
    }

    if($this->opertation == self::PREGMATCH) {
     return preg_match($this->pattern, $this->value);
    }

  }

  function getKey() {
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
