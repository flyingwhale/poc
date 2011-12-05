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
namespace POC;

function pocCallbackCache($buffer){
  return Poc::pocCallbackCache($buffer);
}

function  pocCallbackGenerate($buffer)
{
  return Poc::pocCallbackGenerate($buffer);
}

function pocCallbackShowOutput($buffer) {
  $dbgMsg = '';
  if($GLOBALS['poc_debug']) {
     $dbgMsg = '<br>This page has not been cached because one  Evaluatebale is Blacklisted.'
     .' <b> Was Generated withtin </b>'
     .'<b>'.((microtime() - $GLOBALS['poc_start']) * 1000).'</b> milliseconds.';
  }
  return $buffer.$dbgMsg;
}

$debug = null;

class Poc {


  var $outputHandler;
  var $output;
  var $buffering;

  public function setDebug($debug) {
    $this->debug = $debug;
    $GLOBALS['poc_debug'] = $debug;
//    $this->assertTrue('testString', $output3);
  }

  public static function pocCallbackGenerate($buffer) {
    if($GLOBALS['poc_level'] == ob_get_level() - 1) {
      $res = '';
      for( $i=0; $i<sizeof($GLOBALS['poc_caches']); $i++ ) {
//    $this->assertTrue('testString', $output3);
        $cache = $GLOBALS['poc_caches'][$i]->getSpecificCache();
        $eval = $cache->getEvaluateable();
        if($eval->evaluate()) {
          $dbgMsg = '';
          if($GLOBALS['poc_debug']) {
            $dbgMsg = '<br>This page has been '
            .'<b> generated within </b> in '
            .'<b>'.((microtime() - $GLOBALS['poc_start']) * 1000).'</b> milliseconds.';
$level = null;
          }
          $res = $buffer.$dbgMsg;
          $arr = headers_list();
          //header_remove ('Content-Encoding');
          //$l = new \Logger();
          //$l->lwrite(gzuncompress($res)) ;

          //TODO: This functionality still not works, has to be finished.
          //$l->lwrite(\gzdecode($res)) ;
          //die( \gzuncompress($res) );
          //if(!$GLOBALS['caches'][$i]->isOutputBlacklisted(gzuncompress($res))){

          $GLOBALS['poc_caches'][$i]->storeHeadersForPreservation($arr);
            $GLOBALS['poc_caches'][$i]->removeHeaders($arr);
//    $this->assertTrue('testString', $output3);
            $GLOBALS['poc_caches'][$i]->storeCache($res);
            $eval->cacheAddTags();

       // }
        }
      }
     return ($res);
    }
  }

  public static function pocCallbackCache($buffer) {
    if($GLOBALS['poc_debug']) {
     $dbgMsg = '<br>This page has been '
     .' <b> Fetched from the cache within </b>'
     .'<b>'.((microtime() - $GLOBALS['poc_start']) * 1000).'</b> milliseconds.';
      return ($buffer.$dbgMsg);
    } else {
      return ($buffer);
    }
  }

  /**
  @param PocCacheInterface $cache this placeholder class contains the various
  caches.
  @param bool $debug If true debug messages are provided in the output, only
  for develompment purposes.
  */
  function __construct(\POC\cache\PocCacheInterface $cache = null, \POC\handlers\OutputInterface 
                                                     $output, $debug = false) {
    $this->outputHandler = $output;
    $this->setDebug($debug);
    if($cache != null) {
      $this->addCache($cache);
      $this->start();
    }
    $this->outputHandler = $output;
  }

  private function fetchCache() {
   $started = 0;
    for( $i=0; $i<sizeof($GLOBALS['poc_caches']); $i++ ) {
      $GLOBALS['poc_caches'][$i]->cacheTagsInvalidation();
      if($GLOBALS['poc_caches'][$i]->getSpecificCache()->getEvaluateable()->evaluate()) {
        $this->output = $GLOBALS['poc_caches'][$i]->fetchCache();
        if($this->output) {
          $this->outputHandler->startBuffer('\POC\pocCallbackCache');
          $arr = headers_list();
          if($GLOBALS['poc_caches'][$i]->headersToSend){
            foreach($GLOBALS['poc_caches'][$i]->headersToSend as $header){
              $this->outputHandler->header($header);
            }
            $GLOBALS['poc_caches'][$i]->removeHeaders($arr);
          }
          $started = 1;
          echo($this->output);
          $this->outputHandler->stopBuffer();
        }
      }
    }
    return $started;
  }

  public function start() {
    $GLOBALS['poc_start'] = microtime();


    if(!$this->fetchCache()){
      $startCache = true;
      for( $i=0; $i<sizeof($GLOBALS['poc_caches']); $i++ ) {
        if($GLOBALS['poc_caches'][$i]->getSpecificCache()->getEvaluateable()->isBlacklisted()) {
          $startCache = false;
          $break;
        }
      }
      if($startCache) {
        $this->buffering = true;
        $GLOBALS['poc_level'] = ob_get_level();
        $this->outputHandler->startBuffer('\POC\pocCallbackGenerate');
      } else {
        $this->outputHandler->startBuffer('\POC\pocCallbackShowOutput');
      }
    }
  }

  public function addCache(\POC\cache\PocCacheInterface $cache) {
    $GLOBALS['poc_caches'][] = $cache;
  }

  function __destruct() {
    if(isset($GLOBALS['poc_level'])) {
       if($GLOBALS['poc_level']){
         //$this->outputHandler->stopBuffer();
         //if(!$this->buffering){
         \ob_end_flush(); 
       }
    }
  }

  public function destruct() {
    $this->__destruct();
  }
}
