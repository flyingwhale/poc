<?php
class ApcCache extends PobCacheAbstract {


  public function cacheSpecificFetch() {
    return apc_fetch($this->key);
  }

  public function cacheSpecificClear() {
    //TODO:implementation.
  }
  
  public function cacheSpecificStore($output, $ttl) {
    apc_add ($this->key, $output, $ttl);
  }

  public function cacheSpecificCheck() {
    return apc_exist($this->key);
  }


}
