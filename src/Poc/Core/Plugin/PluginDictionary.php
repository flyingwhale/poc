<?php
namespace Poc\Core\Plugin;

class PluginDictionary {

  private $entityCollection;
  private static $instance;
  private $count = 0;
  
  function __construct(){
    $this->entityCollection = array();
  }
  
  function addEntity($key, PluginEentityElement $entiy){
      $this->entityCollection[$key][] = $entiy;
  }
  
  function runEntity($key){
    if(isset($this->entityCollection[$key])){
      foreach($this->entityCollection[$key] as $entity){
          $entity->invoke();
      }
    }
  }


  public static function getIstance()
  {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    return self::$instance;
  }
  
  public function increment()
  {
    return $this->count++;
  }
  
  public function __clone()
  {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }
  
  public function __wakeup()
  {
    trigger_error('Unserializing is not allowed.', E_USER_ERROR);
  }
  
}
