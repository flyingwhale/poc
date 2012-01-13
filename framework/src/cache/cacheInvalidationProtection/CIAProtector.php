<?php
namespace framework\src\cache\cacheInvaludationProtection;

use POC\cache\tagging\driver\mysql\model\Cache;

class CIAProtector
{
  const KEY_POSTFIX = "ci";

  /**
   * 
   * @var \POC\cache\cacheimplementation\Cache
   */
   private $cache = null;
   
  /**
   * 
   * @param  \POC\cache\cacheimplementation\Cache $cache
   */
  function __construct ($cache)
  {
    $this->setCache($cache);
  }
  
  /**
   * 
   * @param Cache $cache
   */
  function setCache($cache){
    $this->cache = $cache;
  }
  
  public function setSentinel($cnt = 1){
    $this->cache->cacheSpecificStore($this->getKey(), $cnt);
  }
  
  public function getSentinel(){
    $sentiel = $this->cache->fetch($this->getKey());
    
    if($this->cache->fetch($sentiel)){
      $this->setSentinel($sentiel + 1);
    }
    
    return ($sentiel);
  }
  
  private function getKey(){
    return $this->cache->getHasher()->getKey().self::KEY_POSTFIX;
  }
  
  public function deleteSentinel(){
    $this->cache->clearItem($this->getKey());
  }
}

?>