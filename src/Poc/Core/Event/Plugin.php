<?php
namespace Poc\Core\Event;


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