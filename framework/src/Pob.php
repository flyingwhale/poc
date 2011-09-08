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

function PobcallbackCache($buffer){
  return Pob::PobcallbackCache($buffer);
}

function  PobcallbackGenerate($buffer)
{
  return Pob::PobcallbackGenerate($buffer);
}

function PobcallbackShowOutput($buffer) {
  $dbgMsg = '';
  if($GLOBALS['debug']) {
     $dbgMsg = '<br>This page has not been cached because one  Evaluatebale is Blacklisted.'
     .' <b> Was Generated withtin </b>'
     .'<b>'.((microtime() - $GLOBALS['start']) * 1000).'</b> milliseconds.';
  }
  return $buffer.$dbgMsg;
}

$caches = null;
$level = null;
$debug = null;
$start = null;

class Pob {

  const DEF_CACHE_FUNCTION_NAME = '\POC\PobcallbackCache';
  const DEF_GENERATE_FUNCTION_NAME = '\POC\PobcallbackGenerate';
  const DEF_SHOW_OUTPUT_FUNCTION_NAME = '\POC\PobcallbackShowOutput';

  var $outputHandler;
  var $output;
  var $buffering;

  var $callbackCacheFunctionName;
  var $callbackGenerateFunctionName;
  var $callbackShowOutputFunctionName;

  public function setCallbackCacheFunctionName($fn){
    $this->callbackCacheFunctionName = $fn;
  }

  public function setCallbackGenerateFunctionName($fn){
    $this->callbackGenerateFunctionName = $fn;
  }

  public function setCallbackShowOutputFunctionName($fn){
    $this->callbackShowOutputFunctionName = $fn;
  }

  public function setOutputHandler(\OutputInterface $oh){
    $this->outputHandler = $oh;
  }

  public function setDebug($debug) {
    $GLOBALS['debug'] = $debug;
  }

  public static function PobcallbackGenerate($buffer) {
    if($GLOBALS['level'] == ob_get_level() - 1) {
      $res = '';
      for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
        $cache = $GLOBALS['caches'][$i]->getSpecificCache();
        $eval = $cache->getEvaluateable();
        if($eval->evaluate()) {
          $dbgMsg = '';
          if($GLOBALS['debug']) {
            $dbgMsg = '<br>This page has been '
            .'<b> generated within </b> in '
            .'<b>'.((microtime() - $GLOBALS['start']) * 1000).'</b> milliseconds.';
          }
          $res = $buffer.$dbgMsg;
          $arr = headers_list();
          $GLOBALS['caches'][$i]->storeHeadersForPreservation($arr);
          $GLOBALS['caches'][$i]->storeCache($res);
          $eval->cacheAddTags();
        }
      }
     return ($res);
    }
  }

  public static function PobcallbackCache($buffer) {
    if($GLOBALS['debug']) {
     $dbgMsg = '<br>This page has been '
     .' <b> Fetched from the cache within </b>'
     .'<b>'.((microtime() - $GLOBALS['start']) * 1000).'</b> milliseconds.';
      return ($buffer.$dbgMsg);
    } else {
      return ($buffer);
    }
  }

  /**
  @param PobCacheInterface $cache this placeholder class contains the various
  caches.
  @param bool $debug If true debug messages are provided in the output, only
  for develompment purposes.
  */
  function __construct(\PobCacheInterface $cache = null, \OutputInterface $output,
                                                                $debug = false) {
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
    for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      $GLOBALS['caches'][$i]->cacheTagsInvalidation();
      if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluateable()->evaluate()) {
        $this->output = $GLOBALS['caches'][$i]->fetchCache();
        if($this->output) {
          $this->outputHandler->startBuffer($this->callbackCacheFunctionName);
          \header('Cache-Control: no-cache, must-revalidate'); 
          \header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); 
          //$last_modified = \gmdate('D, d M Y H:i:S');
          //\header('Last-Modified: '.$last_modified.' GMT');
          if($GLOBALS['caches'][$i]->headersToSend){
            foreach($GLOBALS['caches'][$i]->headersToSend as $header){
              \header($header);
              //$l = new Logger();
              //$l->lwrite($header." DDD");
            }
          }
          
          $started = 1;
          echo($this->output);
          $this->outputHandler->stopBuffer();
          //\ob_end_flush();
        }
      }
    }
    return $started;
  }

  public function start() {
    $GLOBALS['start'] = microtime();

    if(!$this->callbackCacheFunctionName){
      $this->setCallbackCacheFunctionName(self::DEF_CACHE_FUNCTION_NAME);
    }

    if(!$this->callbackGenerateFunctionName){
      $this->setCallbackGenerateFunctionName(self::DEF_GENERATE_FUNCTION_NAME);
    }

    if(!$this->callbackShowOutputFunctionName){
      $this->setCallbackShowOutputFunctionName(self::DEF_SHOW_OUTPUT_FUNCTION_NAME);
    }

    if(!$this->fetchCache()){
      $startCache = true;
      for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
        if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluateable()->isBlacklisted()) {
          $startCache = false;
          $break;
        }
      }
      if($startCache) {
        $this->buffering = true;
        $GLOBALS['level'] = ob_get_level();
        $this->outputHandler->startBuffer($this->callbackGenerateFunctionName);
      } else {
        $this->outputHandler->startBuffer($this->callbackShowOutputFunctionName);
      }
    }
  }

  public function addCache(\PobCacheInterface $cache) {
    $GLOBALS['caches'][] = $cache;
  }

  function __destruct() {
    if(isset($GLOBALS['level'])) {
    \ob_end_flush();
    }
  }

  public function destruct() {
    $this->__destruct();
  }
}
