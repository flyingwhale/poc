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

/**
 * This cacheengine stores the caches in the Filesystem.
 * 
 * @author Imre Toth
 *
 */
namespace Poc\Cache\CacheImplementation;
use Poc\Core\Optioner;

class FileCache extends Cache {

  const PARAM_DIRECTORY = 'directory';
   
  const KEY_PREFIX = 'POB_CACHE#';
  const TTL_PREFIX = 'POB_CACHE_TTL#';

  private $file;
  private $fileTtl;
  private $tagDb;
  private $directory;
 
  function fillDefaults(){
    parent::fillDefaults();
    $this->optionAble[self::PARAM_DIRECTORY] = '/tmp/';
  }
  
  function __construct($options = array()) {
    parent::__construct($options);
    $this->file = $this->optionAble->getOption(self::PARAM_DIRECTORY).self::KEY_PREFIX;
    $this->fileTtl = $this->optionAble->getOption(self::PARAM_DIRECTORY).self::TTL_PREFIX;
    $this->directory = $this->optionAble->getOption(self::PARAM_DIRECTORY);
    $this->throwDbException();
  }

  public function fetch($key) {
    if($this->checkTtl($key)) {
      $handle = fopen($this->file.$key, "r");
      return fread($handle, filesize($this->file.$key));
    }
  }

  public function clearAll() {

     array_map( "unlink", glob($this->directory.''.self::KEY_PREFIX.'*')  );
     array_map( "unlink", glob($this->directory.''.self::TTL_PREFIX.'*')  );

   }

  public function clearItem($key) {
    if($this->checkTtl($key)) {
      unlink($this->file.$key);
      unlink($this->fileTtl.$key);
    }
  }

  public function cacheSpecificStore($key,$output) {
    $fp = fopen($this->file.$key, 'w');
    fwrite($fp, $output);
    fclose($fp);
    $this->writeTtl($this->ttl,$key);
  }

  public function writeTtl($ttl,$key){
    $fp = fopen($this->fileTtl.$key, 'w');
    fwrite($fp, time()+$ttl);
    fclose($fp);
  }

  public function checkTtl($key){
    if(file_exists($this->fileTtl.$key)){
      $handle = fopen($this->fileTtl.$key, "r");
      $ttl=fread($handle, filesize($this->fileTtl.$key));
      if((int) $ttl>=time()){
        return true;
      } else {
        unlink($this->file.$key);
        unlink($this->fileTtl.$key);
      }
    }
    else return false;
  }

  function  isCacheAvailable(){
    return is_writable($this->directory);
  }
}
