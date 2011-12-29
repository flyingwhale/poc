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

namespace POC\cache;

use POC\cache\cacheimplementation\AbstractPocCacheSpecific;

class PocCache {

  var $specificCache;
  var $headersToStore;
  var $headersToSend;
  var $eTag;
  private $isEtagGeneration = 1;

    
  //TODO: still not works
  public function etagGeneration($output){
    if($this->isEtagGeneration){
      $etag = md5($output);
      $this->headersToStore[] = 'Etag : '.$etag;
      return $etag;
    }
  }
  
  
  function __construct (AbstractPocCacheSpecific $specificCache) {
    $this->specificCache = $specificCache;
  }

  public function storeCache($output) {
    if ($this->getSpecificCache()->getFilter()->evaluate()) {
      $this->specificCache->cacheSpecificStore(
                   $this->specificCache->getHasher()->getKey(), $output);
       //TODO: still not working.
       if($this->isEtagGeneration){
         $this->specificCache->cacheSpecificStore(
           $this->specificCache->getHasher()->getKey().'e',
                                                $this->etagGeneration($output));
       }

       if($this->headersToStore){
         $this->specificCache->cacheSpecificStore(
           $this->specificCache->getHasher()->getKey().'h',
                                              serialize($this->headersToStore));
      }
    }
  }

  public function fetchCache() {
    if($this->getSpecificCache()->getFilter()->evaluate()){
      $this->headersToSend = unserialize($this->specificCache->cacheSpecificFetch(
                        $this->specificCache->getHasher()->getKey().'h'));
      $this->eTag = ($this->specificCache->cacheSpecificFetch(
                        $this->specificCache->getHasher()->getKey().'e'));
      return $this->specificCache->cacheSpecificFetch(
                             $this->specificCache->getHasher()->getKey());
    }
  }

  public function getSpecificCache() {
    return $this->specificCache;
  }

}
