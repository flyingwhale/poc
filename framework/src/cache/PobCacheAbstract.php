<?php

abstract class PobCacheAbstract implements PobCacheInterface,
                                                     PobCacheSpecificInterface {
  
  var $evaluatable;
  var $key;
  
  function __construct(Evaluatable $evaluatable){
    $this->evaluatable = $evaluatable;
    $this->key = $this->generateKey();
  }
  
  function getEvaluatable() {
    return $this->evaluatable;
  }
  
  public function storeCache ($output, $ttl) {
    if ($this->evaluatable->evaluate()) {
       $this->cacheSpecificStore($output, $ttl);
    }
  }

  public function fetchCache () {
    if($this->evaluatable->evaluate()){
      return $this->cacheSpecificFetch();
    }
  }

  public function clearCache () {
    if($this->evaluatable->evaluate()){
      $this->cacheSpecificClear();
    }
  }

  public function generateKey () {
    $key=var_export($this->evaluatable,true);
    return md5($key);
  }

  public function checkKey () {
      return($this->cacheSpecificCheck());
  }
  
}
