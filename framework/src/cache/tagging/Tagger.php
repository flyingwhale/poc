<?php
class Tagger {

  private $evaluateable;
  private $tags;
  private $logger;

  function __construct($tags, Evaluateable $evaluateable) {
      $this->tags = $tags;
      $this->evaluateable = $evaluateable;
      $this->logger = new Logger('/tmp/logs/logTagger');
  }

  function tagCache(){
    $this->getTagDb()->addCacheToTags($this->tags, $this->evaluateable->getKey());
    $this->logger->lwrite("TagCache()");
  }

  function cacheInvalidation(){
    $this->logger->lwrite("cacheInvalidation()");
    $this->getTagDb()->tagInvalidate($this->tags);
  }

  private function getTagDb(){
    return $this->evaluateable->getMyCache()->getTagDb();
    $this->logger->lwrite("getTaDB()");
  }
}