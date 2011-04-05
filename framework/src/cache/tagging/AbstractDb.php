<?php
abstract class AbstractDb {

  function __construct() {
    if(!$this->checkdb()) {
      $this->createDb();
    } else {
      $this->flushOutdated();
    }
  }

  public abstract function checkDb();
  public abstract function createDb();
  protected abstract function addTags($tags);
  public abstract function addCacheToTags($tags);
  public abstract function TagInvalidate();
  public abstract function flushOutdated();
}