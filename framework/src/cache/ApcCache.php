<?php
class ApcCache extends AbstractPobCacheSpecific {
  
  function __construct(Evaluatable $evaluatable,$ttl) {
    $this->ttl = $ttl;
    $this->evaluatable = $evaluatable;
  }
  
  public function cacheSpecificFetch($key) {
    return apc_fetch($key);
  }

  public function cacheSpecificClear($key) {
    //TODO:implementation.
  }
  
  public function cacheSpecificStore($key, $output) {
    apc_add ($key, $output, $this->ttl);
  }

}
