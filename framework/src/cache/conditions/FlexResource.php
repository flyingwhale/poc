<?php

class FlexResource extends Resource {

  var $flexResource;
  
  function __construct ($pattern, $flexResource, $operation=self::EQUALATION) {
    parent::__construct($pattern, $operation);
    $this->pattern = $pattern;
    $this->opertation = $operation;
    $this->flexResource = $flexResource;
    $this->value = $this->setValue();
  }
  
  function setValue() {
    return eval("return $this->flexResource;");
  }
  
}
