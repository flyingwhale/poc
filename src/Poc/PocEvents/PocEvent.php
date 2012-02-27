<?php
namespace Poc\PocEvents;

use Poc\Poc;

use Symfony\Component\EventDispatcher\Event;

class PocEvent extends Event{
  
  /**
   * @var Poc
   */
  private $poc;

  /**
   * @return the $poc
   */
  public function getPoc() {
    return $this->poc;
  }

  function __construct($Poc){
    $this->poc = $Poc;
  }

}
