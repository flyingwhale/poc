<?php
class ServerOutput implements OutputInterface {

  function getLevel(){
    return ob_get_level();
  }

  function startBuffer($callbackFunctname){
    ob_start($callbackFunctname);
  }

  function StopBuffer(){
    die();
  }

}