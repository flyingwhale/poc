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

namespace POC\cache\filtering;

class Evaluateable extends HasValue {

  const OP_EQUALATION = 1;
  const OP_PREGMATCH = 2;

  private $operation;
  private $conditonArray = array();
  private $distinguishVariables = array();
  private $blacklistConditions = array();
  private $cacheAddTags = array();
  private $cacheInvalidationTags = array();

  private $key = null;
  private $pattern;
  private $myCache;

  function getMyCache(){
    return $this->myCache;
  }

  function setMyCache($cache){
    $this->myCache = $cache;
  }

  function __construct($pattern = 1, $value = 1, $operation=self::OP_EQUALATION)  {

    $this->conditonArray[] = $this;
    $this->pattern = $pattern;
    $this->opertation = $operation;
    $this->setValue($value);
  }

  public function evaluate() {
    return $this->conditonArray[0]->selfEvaluate();
  }

  private function selfEvaluate() {
    $this->blacklistCacheInvalidation();
    if($this->opertation == self::OP_EQUALATION) {
      return ($this->pattern == $this->value);
    } elseif ($this->opertation == self::OP_PREGMATCH) {
      return preg_match($this->pattern, $this->value);
    }
  }

  public function getKey() {
    if(!$this->key) {
      $this->key = $this->toHash();
    }
    return $this->key;
  }

  public function addDistinguishVariable($var){
    $this->distinguishVariables[] = $var;
  }

  public function addBlacklistCondition($var){
    $this->blacklistConditions[] = $var;
  }

  public function addCacheTags($condition,$tags){
    if(isset($condition)){
      if($condition){
        $this->cacheAddTags[] = new \Tagger($tags, $this);
      }
    }
  }

  public function addCacheInvalidationTags($condition,$tags){
    if($condition){
      $this->cacheInvalidationTags[] = new \Tagger($tags, $this);
    }
  }

  public function cacheAddTags(){
    foreach($this->cacheAddTags as $tagger){
      $tagger->tagCache();
    }
  }

  public function cacheTagsInvalidation(){
    foreach($this->cacheInvalidationTags as $tagger){
      $tagger->cacheInvalidation();
    }
  }

  public function isBlacklisted() {
      foreach($this->blacklistConditions as $blackRequest) {
      if($blackRequest) {
         return true;
         break;
      }
    }
    return false;
  }

  private function blacklistCacheInvalidation(){
    if($this->isBlacklisted()) {
      $this->myCache->cacheSpecificClearItem($this->getKey());
      return true;
    }
    return false;
  }

  function toString(){
    return serialize($this->distinguishVariables).$this->pattern.$this->value.$this->operation;
  }

}
