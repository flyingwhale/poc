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
use POC\core\Optioner;

require_once 'Rediska.php';

class RediskaCache extends Cache {

  private $rediska;
  private $isNotConnected;

  function fillDefaults(){
    parent::fillDefaults();
    $this->optionAble['servers'] = array('servers' =>
                                     array(array('host'=>'localhost',
                                                              'port'=>'6379')));
  }
  
  function __construct($options = array()) {
    parent::__construct($options);

    $className = 'Rediska';
    if(!class_exists($className)) {
      throw new Exception(sprintf("%s class not exists", $className));
    }

    $this->rediska = new $className($this->optionAble->getOption('servers'));
    $this->isNotConnected = 1;
  }

  public function fetch($key) {
    $keyObj = new \Rediska_Key($key);
    $value = $keyObj->getValue();
    if (is_array($value)){
      $value = serialize($value);
    }
    return $value;
  }

  public function clearAll() {
    $this->rediska->flushdb();
  }

  public function clearItem($key) {
    $keyObj = new \Rediska_Key($key);
    $keyObj->delete();
  }

  public function cacheSpecificStore($key, $output) {
    $keyObj = new \Rediska_Key($key);
    $keyObj->setValue($output);
    $keyObj->expire($this->ttl);

  }

  function  isCacheAvailable(){
    return $this->isNotConnected;
  }

}
