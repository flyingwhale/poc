<?php

class Pob {
  
  var $caches; 
  var $outout;
  var $ttl;
  var $buffering;
  
  function callback($buffer)
  {
    for( $i=0; $i<sizeof($this->caches); $i++ ) {
      if($this->caches[$i]->getEvaluatable()->evaluate()) {
        $buffer.=' ____CACHED______';
        $this->caches[$i]->storeCache($buffer,$this->ttl);
      }
    }
    return ($buffer);
  }
  
  function __construct(PobCacheInterface $cache,$ttl) {
    $this->start = microtime();
    $this->caches[] = $cache;
    $this->ttl = $ttl;
    
    for( $i=0; $i<sizeof($this->caches); $i++ ) {

      if($this->caches[$i]->getEvaluatable()->evaluate()) {
        $this->output = $this->caches[$i]->fetchCache();
        if($this->output) {
          ob_start();
          echo($this->output);
          die();
        }
      }
    }
    $this->buffering=true;
    ob_start('SELF::callback');
  }

  function __destruct() {
    echo('<br>'.(microtime() - $this->start)*1000);
    
    if($this->buffering){
      echo(' generated');
    }
    else{
      echo(' cached');
    }
    ob_flush();
  }

}

