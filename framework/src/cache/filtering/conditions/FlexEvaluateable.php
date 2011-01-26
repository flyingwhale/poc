<?php

class FlexEvaluateable extends Evaluateable {

  var $flexResource;

  function __construct ($pattern, $flexResource, $operation=self::EQUALATION) {
    parent::__construct($pattern, $operation);
    $this->flexResource = $flexResource;
    $this->setValueDescription($flexResource);
    $this->value = $this->setValue();
  }

  function setValue() {
    return eval("return $this->flexResource;");
  }
}
