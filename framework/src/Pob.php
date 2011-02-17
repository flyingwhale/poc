<?php

  function PobcallbackCache($buffer) {
    return Pob::PobcallbackCache($buffer);
  }

  function PobcallbackGenerate($buffer)
  {
    return Pob::PobcallbackGenerate($buffer);
  }

  $caches;

class Pob {

  var $outout;

  var $buffering;
  var $foundMatch;
  var $level;
  var $start;

  public static function PobcallbackCache($buffer)
  {
    for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluatable()->evaluate()) {
        $GLOBALS['caches'][$i]->storeCache($buffer);
      }
    }
    return ($buffer);
  }

  public static function PobcallbackGenerate($buffer)
  {
    return ($buffer);
  }

  function __construct(PobCacheInterface $cache=null) {
    $this->start = microtime();
    if($cache != null) {
      $this->addCache($cache);
      $this->start();
    }
  }

  public function start() {
      for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluatable()->evaluate()) {
        $this->output = $GLOBALS['caches'][$i]->fetchCache();
        if($this->output) {
          ob_start('PobcallbackGenerate');
          echo($this->output);
          die();
        }
      }
    }
    $this->buffering=true;
    ob_start('PobcallbackCache');
  }  

  function addCache(PobCacheInterface $cache) {
    $GLOBALS['caches'][] = $cache;
  }
  
  function __destruct() {
    echo('<br>This page has been ');
    if($this->buffering){
       echo(' <b> generated </b>');
    }
    else{
      echo(' fetched from the <b>cache</b> within ');
    }

    echo('<b>'.((microtime() - $this->start) * 1000).'</b> milliseconds.');

    ob_end_flush();
  }
}

