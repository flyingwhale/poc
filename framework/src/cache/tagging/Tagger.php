<?php

use POC\cache\filtering\Evaluateable;

class Tagger {

  private $evaluateable;
  private $tags;

  function __construct($tags, Evaluateable $evaluateable) {
      $this->tags = $tags;
      $this->evaluateable = $evaluateable;
  }

  function tagCache(){
    $this->getTagDb()->addCacheToTags($this->tags, $this->evaluateable->getKey());
  }

  function cacheInvalidation(){
    $this->getTagDb()->tagInvalidate($this->tags);
  }

  private function getTagDb(){
    return $this->evaluateable->getMyCache()->getTagDb();
  }
}
