<?php
namespace POC\cache\tagging\driver\mysql\model;

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