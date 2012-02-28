<?php
namespace Poc\Plugins\TestPlugin;

require_once ('src/Poc/Core/Plugin/EventEntity.php');

use Poc\Core\Plugin\EventEentity;

class invoke1 implements EventEentity {
  
  function invoke(){
    echo("ZIZI"); 
  }
    
}
