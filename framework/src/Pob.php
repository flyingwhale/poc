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

$caches = null;
$level = null;
$debug = null;
$start = null;

class Pob {
  var $outout;
  var $buffering;
  var $foundMatch;
  var $start;
  var $started;

  public function setDebug($debug) {
    $GLOBALS['debug'] = $debug;
  }

  public static function PobcallbackCache($buffer)
  {
    if($GLOBALS['level'] == ob_get_level() - 1) {
      for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
        if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluatable()->evaluate()) {
         if($GLOBALS['debug']) {
           $dbgMsg = '<br>This page has been '
           .'<b> generated </b> in '
           .'<b>'.((microtime() - $GLOBALS['start']) * 1000).'</b> milliseconds.';
           $res = $buffer.$dbgMsg;
           $GLOBALS['caches'][$i]->storeCache($res);
         } else {
            $res = $buffer;
            $GLOBALS['caches'][$i]->storeCache($res);
          }
        }
      }
      //return ($buffer." ------------ ".$GLOBALS['level']." -------".ob_get_level());
      return ($res);
    }
  }

  public static function PobcallbackGenerate($buffer) {
    if($GLOBALS['debug']) {
     $dbgMsg = '<br>This page has been '
     .' <b> Fetched from the cache </b>'
     .'<b>'.((microtime() - $GLOBALS['start']) * 1000).'</b> milliseconds.';

      return ($buffer.$dbgMsg);
    } else {
      return ($buffer);
    }
  }

  function __construct(PobCacheInterface $cache = null,$debug = false) {
    $GLOBALS['start'] = microtime();
    if($debug) {
      $this->setDebug($debug);
    }
    if($cache != null) {
      $this->addCache($cache);
      $this->start();
    }
  }

  public function start() {
    $this->started = 1;
    for( $i=0; $i<sizeof($GLOBALS['caches']); $i++ ) {
      if($GLOBALS['caches'][$i]->getSpecificCache()->getEvaluatable()->evaluate()) {
        $this->output = $GLOBALS['caches'][$i]->fetchCache();
        if($this->output) {
          header('Cache-Control: no-cache, must-revalidate'); 
          header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); 
          $last_modified = gmdate('D, d M Y H:i:S');
          header('Last-Modified: '.$last_modified.' GMT');
          ob_start('PobcallbackGenerate');
          echo($this->output);
          die();
        }
      }
    }
    $this->buffering=true;
    $GLOBALS['level'] = ob_get_level();
    ob_start('PobcallbackCache');
  }

  public function addCache(PobCacheInterface $cache) {
    $GLOBALS['caches'][] = $cache;
  }

  function __destruct() {

    if($this->debug) {
      if($this->started)
      {
        echo('<br>This page has been ');
        echo(' <b> generated </b>');
        echo('<b>'.((microtime() - $this->start) * 1000).'</b> milliseconds.');
      } else {
        echo('<b>You have not called the start() method of your Pob instance!!</b>');
      }
    } 
    ob_end_flush();
  }
}

