<?php

namespace unittest\handler;

class TestOutput implements \OutputInterface {

  function getLevel(){
    return ob_get_level();
  }

  function startBuffer($callbackFunctname){
    ob_start($callbackFunctname);
  }

  function stopBuffer(){
    ob_flush();
  }

}
