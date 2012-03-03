<?php
namespace Poc\Events;

use Poc\Poc;

use Symfony\Component\EventDispatcher\Event;

class BaseEvent extends Event{
  
  /**
   * @var Poc
   */
  private $event;

  /**
   * @return Poc
   */
  public function getEvent() {
    return $this->event;
  }

  function __construct($Poc){
    $this->event = $Poc;
  }

}
