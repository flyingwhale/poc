<?php
namespace Poc\Handlers;

use Poc\Handlers\OutputInterface;

abstract class Output implements OutputInterface
{
  protected $poc;
  
  function setPoc($poc){
    $this->poc = $poc;
  }
  
}

?>