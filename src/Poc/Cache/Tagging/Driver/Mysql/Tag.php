<?php
namespace Poc\Cache\Tagging\Driver\Mysql;

class Tag
{
  private $caches = array();
  
  public $id       = null;
  public $tag     = null;
  
  public function setCaches($caches)
  {
    $this->caches = $caches;
  }
  
  public function getCaches()
  {
    return $this->caches;
    
  }
}

?>