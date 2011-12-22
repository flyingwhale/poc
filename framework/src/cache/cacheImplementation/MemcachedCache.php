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
use POC\core\Optioner;

class MemcachedCache extends AbstractPocCacheSpecific {

  private $memcache;
  private $compression=false;
  private $currentResult;
  private $isConnected;
  protected $defaultOptions = array('server'=>'localhost',
                                    'port'=>'11211',
                                   // ''=>'',
                                   // ''=>'',
                                   );

  function __construct(Evaluateable $evaluatable, $ttl, $options = array()) {
    $this->options = $options;
    new Optioner($this);
    parent::__construct($evaluatable,$ttl);
    $this->memcache = new Memcache();
    $this->isConnected = $this->memcache->connect($this->options['server'], $this->options['port']);;
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
