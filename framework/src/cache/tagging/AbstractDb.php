<?php
abstract class AbstractDb {

  var $cache;

  function __construct() {
    if(!$this->checkdb()) {
      $this->createDb();
    } else {
      $this->flushOutdated();
    }
  }

  function setCache($cache){
    $this->cache = $cache;
  }

  public abstract function checkDb();
  public abstract function createDb();
  protected abstract function addTags($tags);
  public abstract function addCacheToTags($tags,$key);
  public abstract function TagInvalidate($tags);
  public abstract function flushOutdated();
}