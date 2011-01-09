<?php

class PobCache implements PobCacheInterface {
  
  var $evaluatable;
  var $key;
  var $cache;
  
  function __construct(Evaluatable $evaluatable,
                                             PobCacheSpecificInterface $cache) {
    $this->evaluatable = $evaluatable;
    $this->key = $this->generateKey();
    $this->cache = $cache;
  }
  
  function getEvaluatable() {
    return $this->evaluatable;
  }
  
  public function storeCache ($output) {
    if ($this->evaluatable->evaluate()) {
       $this->cache->cacheSpecificStore($this->key, $output);
    }
  }

  public function fetchCache () {
    if($this->evaluatable->evaluate()){
      return $this->cache->cacheSpecificFetch($this->key);
    }
  }

  public function clearCache () {
    if($this->evaluatable->evaluate()){
      $this->cache->cacheSpecificClear();
    }
  }

  public function generateKey () {
    $key=var_export($this->evaluatable,true);
    return md5($key);
  }
  
  public function getCache() {
    return $this->cache;
  }

  
}
