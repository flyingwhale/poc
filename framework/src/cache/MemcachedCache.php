<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/class MemcachedCache extends AbstractPobCacheSpecific {
  
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
