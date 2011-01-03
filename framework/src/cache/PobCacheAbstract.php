<?php

abstract class PobCacheAbstract implements PobCacheInterface {
  
  var $evaluatable;
  function __construct(Evaluatable $evaluatable){
    $this->evaluatable = $evaluatable;
  }
  
  function getEvaluatable() {
    return $this->evaluatable;
  }
  
}
