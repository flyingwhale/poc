<?php
class ApcCache extends AbstractPobCacheSpecific {
  
  function __construct($ttl) {
    $this->ttl = $ttl;
  }
  
  public function cacheSpecificFetch($key) {
    return apc_fetch($key);
  }

  public function cacheSpecificClear($key) {
    //TODO:implementation.
  }
  
  public function cacheSpecificStore($key, $output) {
    echo($this->ttl).'ZIZIZIZIZIZIZIZI';
    apc_add ($key, $output, $this->ttl);
  }

}
