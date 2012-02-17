<?php
namespace Poc\Plugins\TestPlugin;

require_once ('src/Poc/Core/Plugin/PluginEentityElement.php');

use Poc\Core\Plugin\PluginEentityElement;

class invoke1 implements PluginEentityElement {


  function invoke(){
    echo("ZIZI");
  }

}
