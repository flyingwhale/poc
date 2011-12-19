<?php
namespace POC\handlers;
class ServerOutput implements OutputInterface {

  function getLevel(){
    return ob_get_level();
  }

  function startBuffer($callbackFunctname){
    ob_start(array('\POC\Poc', $callbackFunctname));
  }

  function StopBuffer(){
    die();
  }

  function header($header){
    \header($header);
  }

  function obEnd(){
    \ob_end_flush();
  }

}
