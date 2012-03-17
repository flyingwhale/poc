<?php
namespace Poc\Cache\CacheInvalidationProtection;

use Symfony\Component\EventDispatcher\Event;

class CiaEvent extends Event
{
  /**
   *
   * @var CIAProtector
   */
  protected $cia;

  /**
   * @return the $cia
   */
  public function getCia() {
    return $this->cia;
  }

  function __construct($cia, $msg =''){
    $this->cia = $cia;
    $this->msg = $msg;
  }


}
