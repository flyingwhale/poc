<?php

use POC\cache\filtering\Hasher;

class Tagger {

  private $evaluateable;
  private $tags;
  private $ttl;
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

}
