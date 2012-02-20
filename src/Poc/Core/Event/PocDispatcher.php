<?php
namespace Poc\Core\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

class PocDispatcher {

  /**
   * 
   * @var EventDispatcher
   */
  private static $instance;
  
  private $count = 0;
  
  /**
   * 
   * @return \Symfony\Component\EventDispatcher\EventDispatcher
   */
  public static function getIstance()
  {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new EventDispatcher();
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

?>