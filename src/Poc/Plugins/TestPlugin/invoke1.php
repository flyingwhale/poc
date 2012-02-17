<?php
namespace Poc\Plugins\TestPlugin;

use Poc\Core\Event\EventEntity;

class invoke1 implements EventEntity {
  
  function invoke(){
    echo("ZIZI"); 
  }
    
}
