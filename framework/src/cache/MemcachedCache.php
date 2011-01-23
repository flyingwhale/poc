<?php
class MemcachedCache extends AbstractPobCacheSpecific {
  
  var $memcache;
  var $compression=false;
  var $currentResult;
  
  
  function __construct(Evaluateable $evaluatable, $ttl, $server, $port = 11211) {
    $this->memcache = new Memcache();
    $this->memcache->connect($server, $port) or die ('Could not connect to' 
                                                    + ' the memcached server.');
    $this->ttl = $ttl;
    $this->evaluatable = $evaluatable;
  }

  public function cacheSpecificFetch($key) {
    return $this->memcache->get($key);
  }

  public function cacheSpecificClear($key) {
    $this->memcache->delete($key);
  }
  
  public function cacheSpecificStore($key, $output) {
    //apc_add ($this->key, $output, $ttl);
    $this->memcache->set($key, $output, $this->compression, $this->ttl);
  }

}
