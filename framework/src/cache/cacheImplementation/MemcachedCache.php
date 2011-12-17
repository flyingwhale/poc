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
*/
use POC\cache\filtering\Evaluateable;

class MemcachedCache extends AbstractPocCacheSpecific {

  var $memcache;
  var $compression=false;
  var $currentResult;
  private $isConnected;

  function __construct(Evaluateable $evaluatable, $ttl, $server, $port = 11211) {
    parent::__construct($evaluatable,$ttl);
    $this->memcache = new Memcache();
    $this->isConnected = $this->memcache->connect($server, $port);;
    $this->throwDbException();
  }

  public function cacheSpecificFetch($key) {
    return $this->memcache->get($key);
  }

  public function cacheSpecificClearAll() {
    $this->memcache->flush();
  }

  public function cacheSpecificClearItem($key) {
    $this->memcache->delete($key);
  }

  public function cacheSpecificStore($key, $output) {
    $this->memcache->set($key, $output, $this->compression, $this->ttl);
  }

  function  isCacheAvailable(){
    return $this->isConnected;
  }

}
