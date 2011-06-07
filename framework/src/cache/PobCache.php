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

class PobCache implements PobCacheInterface {

  var $specificCache;

  function __construct (AbstractPobCacheSpecific $specificCache) {
    $this->specificCache = $specificCache;
  }

  public function storeCache($output) {
    if ($this->specificCache->getEvaluateable()->evaluate()) {
     echo " -- s t o r e --";
       $this->specificCache->cacheSpecificStore(
                     $this->specificCache->getEvaluateable()->getKey(), $output);
    }
  }

  public function fetchCache() {
    if($this->specificCache->getEvaluateable()->evaluate()){
      return $this->specificCache->cacheSpecificFetch(
                              $this->specificCache->getEvaluateable()->getKey());
    }
  }

  public function clearCacheAll() {
    if($this->specificCache->getEvaluataeble()->evaluate()){
      $this->specificCache->cacheSpecificClearAll();
    }
  }

  public function clearCacheItem($key) {
    if($this->specificCache->getEvaluateable()->evaluate()){
      $this->specificCache->cacheSpecificClearItem($key);
    }
  }

  public function getSpecificCache() {
    return $this->specificCache;
  }

  public function cacheTagsInvalidation(){
    $this->specificCache->getEvaluateable()->cacheTagsInvalidation();
  }

}
