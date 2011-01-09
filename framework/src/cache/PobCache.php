<?php

class PobCache implements PobCacheInterface {
  

  var $specificCache;
  
  function __construct(AbstractPobCacheSpecific $specificCache) {
    $this->specificCache = $specificCache;
  }
  
  
  public function storeCache ($output) {
    if ($this->specificCache->getEvaluatable()->evaluate()) {
       $this->specificCache->cacheSpecificStore(
                     $this->specificCache->getEvaluatable()->getKey(), $output);
    }
  }

  public function fetchCache () {
    if($this->specificCache->getEvaluatable()->evaluate()){
      return $this->specificCache->cacheSpecificFetch(
                              $this->specificCache->getEvaluatable()->getKey());
    }
  }

  public function clearCache () {
    if($this->specificCache->getEvaluatable()->evaluate()){
      $this->specificCache->cacheSpecificClear();
    }
  }

  public function getSpecificCache() {
    return $this->specificCache;
  }

}
