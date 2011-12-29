<?php
namespace POC\cache\tagging;

abstract class AbstractDb {

  var $cache;

  function __construct()
  {
    $this->flushOutdated();    
  }

  function addCache($cache) {
    $this->cache = $cache;
  }

  protected abstract function initDbStructure();
  protected abstract function createDb();
  protected abstract function createTables();
  public abstract function addCacheToTags($tags,$key, $expires);
  public abstract function TagInvalidate($tags);
  public abstract function flushOutdated();
}
