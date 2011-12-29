<?php
namespace POC\cache\tagging;

use POC\cache\filtering\Hasher;

class Tagger {

  private $tags;
  private $ttl;
  /**
   * 
   * @var AbstractDb;
   */
  private $tagDb;

  function __construct($tags, Hasher $hasher, $tagDb, $ttl) {
      $this->tags = $tags;
      $this->hasher = $hasher;
      $this->ttl = $ttl;
      $this->tagDb = $tagDb;
  }

  function tagCache(){
    $this->tagDb->addCacheToTags($this->tags, $this->hasher->getKey());
  }

  function cacheInvalidation(){
    $this->tagDb->tagInvalidate($this->tags);
  }
  
  function addCache($cache){
    $this->tagDb->addCache($cache);
  }

}
