<?php
namespace Poc\Core\Plugin;

use Poc\Plugins\TestPlugin\invoke1;

use Poc\PocDictionaryEntries;

abstract class Plugin {
  
  /**
   * 
   * @var EventDictionary
   */
  protected $eventDictionary;
  
  function __construct(){
    $this->eventDictionary = EventDictionary::getIstance();
  }
  
  abstract function addEventEntities();

}

?>