<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
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

