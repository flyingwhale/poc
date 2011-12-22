<?php


namespace unittest\handler;

class TestOutput implements \POC\handlers\OutputInterface
{
  private $header = null;
  private $outputFlow = 1;
  private $output = '';

  function getLevel() {
    return ob_get_level();
  }

  function startBuffer($callbackFunctname) {
    ob_start(array('\POC\Poc',$callbackFunctname));
  }

  function stopBuffer() {
    $this->outputFlow = 0;
    \ob_flush();
  }

  function header($header) {
    $this->header = $header;
  }
  public function getHeader() {
    return $this->header;
  }

  function obEnd(){
    $this->outputFlow = 0;
    \ob_flush();
  }

  function getOutputFlow(){
    return $this->outputFlow;
  }

  function cacheCallback($output){
    $this->output = $output;
  }

  function getOutput(){
    return  $this->output;
  }
}

