<?php
  
  function PobcallbackCache($buffer)
  { 
    return Pob::PobcallbackCache($buffer);
  }

  function PobcallbackGenerate($buffer)
  { 
    return Pob::PobcallbackGenerate($buffer);
  }
  
  $caches;
  $ttl;
  
class Pob {
  

  var $outout;
  
  var $buffering;
  var $foundMatch;
  var $level;
  
  public static function PobcallbackCache($buffer)
  { 
    for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      if($GLOBALS['caches'][$i]->getEvaluatable()->evaluate()) {
        $GLOBALS['caches'][$i]->storeCache($buffer,$GLOBALS['ttl']);
      }
    }
    return ($buffer);
  }

  public static function PobcallbackGenerate($buffer)
  { 
    return ($buffer);
  }
  
  function __construct(PobCacheInterface $cache,$ttl) {
 
    $this->start = microtime();
    $GLOBALS['caches'][] = $cache;
    $GLOBALS['ttl'] = $ttl;
    
    for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      if($GLOBALS['caches'][$i]->getEvaluatable()->evaluate()) {
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

  function __destruct() {
      echo('<br>'.(microtime() - $this->start)*1000);
      
      if($this->buffering){
        echo(' generated<br>');
      }
      else{
        echo(' cached<br>');
      }
    ob_end_flush();
    
  }
  

}

