<?php
namespace Poc\Core\Plugin;

use Poc\Plugins\TestPlugin\invoke1;

use Poc\PocDictionaryEntries;

abstract class Plugin {
  
  /**
   * 
   * @var PluginDictionary
   */
  protected $pluginDictionary;
  
  function __construct(){
    $this->pluginDictionary = PluginDictionary::getIstance();
  }
  
  abstract function addEnityElements();

}

?>