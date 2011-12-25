<?php
namespace POC\cache\tagging;

abstract class AbstractDb {

  var $cache;

  function __construct() {
/*    
    if($this->checkdb()) {
      if(!$this->initDbStructure()) {
        $this->createDb();
      }
    } else {
      $this->flushOutdated();
    }
*/    
  }

  function setCache($cache) {
    $this->cache = $cache;
  }

//  public abstract function checkDb();
  protected abstract function initDbStructure();
  protected abstract function createDb();
  protected abstract function createTables();
//  protected abstract function addTags($tags);
  public abstract function addCacheToTags($tags,$key);
  public abstract function TagInvalidate($tags);
  public abstract function flushOutdated();
}
