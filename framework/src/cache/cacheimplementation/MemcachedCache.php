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

namespace POC\cache\cacheimplementation;
use POC\cache\filtering\Evaluateable;
use POC\core\Optioner;

class MemcachedCache extends Cache {

  private $memcache;
  private $compression=false;
  private $isConnected;
  
  function fillDefaults(){    
    parent::fillDefaults();
    $this->optionAble['server'] = 'localhost';
    $this->optionAble['port'] = '11211';     
  }
  
  function __construct($options = array()) {
    parent::__construct($options);
    $this->memcache = new \Memcache();
    $this->isConnected = $this->memcache->connect($this->optionAble->getOption('server'), $this->optionAble->getOption('port'));
    $this->throwDbException();
  }

  public function fetch($key) {
    return $this->memcache->get($key);
  }

  public function clearAll() {
    $this->memcache->flush();
  }

  public function clearItem($key) {
    $this->memcache->delete($key);
  }

  public function cacheSpecificStore($key, $output) {
    $this->memcache->set($key, $output, $this->compression, $this->ttl);
  }

  function  isCacheAvailable(){
    return $this->isConnected;
  }

}
