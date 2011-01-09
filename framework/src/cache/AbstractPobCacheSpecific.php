<?php
abstract class AbstractPobCacheSpecific implements PobCacheSpecificInterface {

  /** This variable must be declared at the constructors of this class.*/
  var $ttl;
  
  var $evaluatable;
  
  function getEvaluatable() {
    return $this->evaluatable;
  }
}
