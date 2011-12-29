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


use POC\cache\PocCache;

use POC\Handlers\OutputInterface;

use POC\cache\header\HeaderManipulator;

use POC\cache\filtering\OutputFilter;

class Poc
{
  static private $outputHandler;
  private $output;
  private $buffering;
  static private $debug;
  static private $start;
  static private $caches;
  static private $level;
  static private $headerManipulator;
  static private $outputFilter;
  
  private function setDebug($debug) {
    self::$debug = $debug;
  }

  public static function pocCallbackShowOutput($buffer) {
    $return = $buffer;
    if (self::$debug) {
       $return .= '<br>This page has not been cached because one  Evaluatebale is
                                                                   Blacklisted.'
       .' <b> Was Generated within </b>'
       .'<b>'.((microtime() - self::$start) * 1000).'</b> milliseconds.';
    }
    self::$outputHandler->cacheCallback($return);
    return $buffer.$dbgMsg;
  }

  public static function pocCallbackGenerate($buffer) {
    if (self::$level == \ob_get_level() - 1) {
      for ( $i=0; $i<sizeof(self::$caches); $i++ ) {
        //TODO:fixit
        //if(self::$caches[$i]->isOutputBlacklisted($buffer))
        if(self::$caches[$i]->getSpecificCache()->getFilter()->evaluate())
        {
            $return = $buffer;

            if (self::$debug) {
              $return .= '<br>This page has been '
              .'<b> generated within </b> in '
              .'<b>'.((microtime() - self::$start) * 1000).
                                                           '</b> milliseconds.';
            }
            //TODO: add it to the OutputHandler.
            $headers = \headers_list();
            //self::$caches[$i]->storeHeadersForPreservation($arr);
            self::$headerManipulator->storeHeadersForPreservation($headers);
            //self::$caches[$i]->removeHeaders($arr);
            self::$headerManipulator->removeHeaders($headers);
            self::$caches[$i]->storeCache($return);
            self::$caches[$i]->getSpecificCache()->cacheAddTags();
          }

      }
      self::$outputHandler->cacheCallback($return);
      return ($return);
    }
  }

  public static function pocCallbackCache($buffer) {
    $return = $buffer;
    if (self::$debug) {
     $return .=  '<br>This page has been '
     .' <b> Fetched from the cache within </b>'
     .'<b>'.((microtime() - self::$start) * 1000).'</b> milliseconds.';
    }
    self::$outputHandler->cacheCallback($return);
    return $return;
  }

  /**
  @param PocCacheInterface $cache this placeholder class contains the various
  caches.
  @param bool $debug If true debug messages are provided in the output, only
  for develompment purposes.
  */
  function __construct( PocCache $cache,
                        OutputInterface $output, 
                        HeaderManipulator $headerManipulator,
                        OutputFilter $outputFilter, $debug = false) {
    self::$headerManipulator = $headerManipulator;
    self::$headerManipulator->setOutputHandler($output);
    self::$outputHandler = $output;
    self::$outputFilter = $outputFilter;
    
    $this->setDebug($debug);
    if ($cache != null) {
      $this->addCache($cache);
    }
    self::$outputHandler = $output;
  }

  private function fetchCache() {
   $started = 0;
    for ( $i=0; $i<sizeof(self::$caches); $i++ ) {
      self::$caches[$i]->getSpecificCache()->cacheTagsInvalidation();
      if (self::$caches[$i]->getSpecificCache()->getFilter()->evaluate()) {
        $this->output = self::$caches[$i]->fetchCache();
        if ($this->output) {
          self::$outputHandler->startBuffer('pocCallbackCache');
          $arr = headers_list();
          if (self::$caches[$i]->headersToSend) {
            foreach (self::$caches[$i]->headersToSend as $header) {
              self::$outputHandler->header($header);
            }
            self::$headerManipulator->removeHeaders($arr);
          }
          $started = 1;
          echo($this->output);
          self::$outputHandler->stopBuffer();
        }
      }
    }
    return $started;
  }

  public function start() {

    self::$start = microtime();

    if (!$this->fetchCache()) {
      $startCache = true;
      for ( $i=0; $i<sizeof(self::$caches); $i++ ) {
        if (self::$caches[$i]->getSpecificCache()->getFilter()->isBlacklisted()) {
          $startCache = false;
          break;
        }
      }
      if ($startCache) {
        $this->buffering = true;
        self::$level = \ob_get_level();
        self::$outputHandler->startBuffer('pocCallbackGenerate');
      } else {
        self::$outputHandler->startBuffer('pocCallbackShowOutput');
      }
    }
  }

  private function addCache(\POC\cache\PocCache $cache) {
    self::$caches[] = $cache;
  }

  public function __destruct() {
    if (isset(self::$level)) {
       if (self::$level) {
         \ob_end_flush();
       }
    }
  }

  public function destruct() {
    $this->__destruct();
  }
}
