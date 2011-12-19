<?php

namespace unittest\handler;

class TestOutput implements \POC\handlers\OutputInterface
{
  private $header = null;

  function getLevel() {
    return ob_get_level();
  }

  function startBuffer($callbackFunctname) {
    ob_start(array('\POC\Poc',$callbackFunctname));
  }

  function stopBuffer() {
    ob_flush();
  }

  function header($header) {
    $this->header = $header;
  }
  public function getHeader() {
    return $this->header;
  }
}

