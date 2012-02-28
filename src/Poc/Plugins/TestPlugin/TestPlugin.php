<?php
namespace Poc\Plugins\TestPlugin;

use Poc\PocDictionaryEntries;

use Poc\Core\Plugin\Plugin;

class TestPlugin extends Plugin{
  
  function addEventEntities(){
    $this->eventDictionary->addEvent(PocDictionaryEntries::POC_DICTIONARY_ENTRY_BEFORE_OUTPUT_SAVE, new invoke1());
    $this->eventDictionary->addEvent(PocDictionaryEntries::POC_DICTIONARY_ENTRY_CONSTRUCTOR_END, new invoke1());
  }
}
