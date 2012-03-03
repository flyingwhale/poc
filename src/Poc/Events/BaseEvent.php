<?php
namespace Poc\Events;

use Poc\Poc;

use Symfony\Component\EventDispatcher\Event;

class BaseEvent extends Event{
  
  /**
   * @var Poc
   */
  private $poc;

  /**
   * @return Poc
   */
  public function getEvent() {
    return $this->poc;
  }

  function __construct($Poc){
    $this->poc = $Poc;
  }

}
