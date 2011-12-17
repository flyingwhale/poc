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

require_once 'Rediska.php';

class RediskaCache extends AbstractPocCacheSpecific {
  
  private $rediska;
  private $isNotConnected;

  function __construct(Evaluateable $evaluatable, $ttl, $options = array()) {
    parent::__construct($evaluatable,$ttl);

    if (!isset($options['servers']))
    {
      $options['servers'] = array(array('host' => 'localhost', 'port' => 6379)); 
    } 

    $className = 'Rediska';
    
    if(!class_exists($className)) {
      throw new Exception(sprintf("%s class not exists", $className));      
    }
    
    $this->rediska = new $className($options);
    $this->isNotConnected = 1;
  }

  public function cacheSpecificFetch($key) {
    $keyObj = new Rediska_Key($key);
    $value = $keyObj->getValue();
    
    return $value;
  }

  public function cacheSpecificClearAll() {
    $this->rediska->flushdb();
  }

  public function cacheSpecificClearItem($key) {
    $keyObj = new Rediska_Key($key);
    $keyObj->delete();
  }

  public function cacheSpecificStore($key, $output) {
    $keyObj = new Rediska_Key($key);
    $keyObj->setValue($output);
    $keyObj->expire($this->ttl);
    
  }

  function  isCacheAvailable(){
    return $this->isNotConnected;
  }

}