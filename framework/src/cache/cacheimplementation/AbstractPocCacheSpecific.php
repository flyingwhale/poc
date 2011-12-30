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

use POC\cache\filtering\Filter;
use POC\cache\filtering\Hasher;
use POC\core\OptionAble;
use POC\cache\tagging\MysqlTagging;
use POC\cache\tagging\Tagger;

abstract class AbstractPocCacheSpecific extends OptionAble implements PocCacheSpecificInterface
{
  /** This variable must be declared at the constructors of this class.*/
  protected $ttl;

  /** The databese that stores the caches*/
  private  $tagDb;

  protected $defaultOptions = array();

  protected $hasher;

  protected $filter;

  protected $cacheInvalidationTags = array();

  protected $cacheAddTags = array();

  function __construct(Hasher $hasher,Filter $filter,$ttl,$tagDb = null) {
     $this->hasher = $hasher;
     $this->ttl = $ttl;
     $this->tagDb = $tagDb;
     $this->filter = $filter;
  }

  /**
   * 
   * @return Filter
   */
  function getFilter(){
    return $this->filter;
  }

  public function addCacheInvalidationTags($condition,$tags){
    if($condition){
      $tagger = new Tagger($tags,$this->hasher,$this->tagDb,$this->ttl);
       $tagger->addCache($this);
       $this->cacheInvalidationTags[] = $tagger;
    }
  }

  public function addCacheAddTags($condition,$tags){
    if($condition){
      $tagger = new Tagger($tags,$this->hasher,$this->tagDb,$this->ttl);
      $tagger->addCache($this);
      $this->cacheAddTags[] = $tagger;
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

  /**
   * 
   * @return Hasher
   */
  function getHasher(){
    return $this->hasher;
  }

  private function setTagDb(AbstractDb $tagDb = null){
    if ($tagDb == null) {
      $this->tagDb = new MysqlTagging();
    } else {
      $this->tagDb = $tagDb;
    }
    $this->tagDb->setCache($this);
  }

  function getTagDb(){
    if (!$this->tagDb) {
      $this->setTagDb();
    }
    return $this->tagDb;
  }

  function throwDbException(){
    if(!$this->isCacheAvailable()){
      throw new \Exception("The cache database is not reachable!");
    }
  }
}
