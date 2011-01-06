<?php
class MemcachedCache extends PobCacheAbstract {
  
  var $memcache;
  var $compression=false;
  var $currentResult;
  
  function __construct(Evaluatable $evaluatable, $server, $port = 11211) {
    parent::__construct($evaluatable);
    $this->memcache = new Memcache;
    $this->memcache->connect('localhost', $port) or die ('Could not connect to' 
                                                    + ' the memcached server.');
  }

  public function cacheSpecificFetch() {
    return $this->memcache->get($this->key);
  }

  public function cacheSpecificClear() {
    $this->memcache->delete($this->key);
  }
  
  public function cacheSpecificStore($output, $ttl) {
    //apc_add ($this->key, $output, $ttl);
    $this->memcache->set($this->key, $output, $this->compression, $ttl);
  }

}
