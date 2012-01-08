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

use POC\core\OptionAbleInterface;

use POC\handlers\ServerOutput;

use POC\cache\cacheimplementation\FileCache;

use POC\core\OptionAble;

use POC\cache\cacheimplementation\AbstractPocCacheSpecific;

use POC\Handlers\OutputInterface;

use POC\cache\header\HeaderManipulator;

use POC\cache\filtering\OutputFilter;

/**
 * This class contains the "Entry point" of the caching process. Therefor is is 
 * a really crucial part of the framework. This class implements the 
 * OptionAbleInterface, and the PocParams
 * 
 * 
 * @package Poc
 * @author Imre Toth
 *
 */
class Poc implements PocParams, OptionAbleInterface
{
  
  /**
   * 
   * @var OutputInterface
   */
  static private $outputHandler = null;
  
  /**
   * 
   * @var String
   */
  private $output = null;

  /**
   * If its value is true teh debug mod is turned on.
   * 
   * @var boolean
   */
  static private $debug = null;
  
  /**
   * When the start function of the class executed sets its value by
   * calling the microtime function.
   * 
   * @var unknown_type
   */
  static private $startTime = null;
  
  /**
   * 
   * @var unknown_type
   */
  static private $level = null;
  
  /**
   * 
   * @var HeaderManipulator
   */
  static private $headerManipulator = null;
  
  /**
   * 
   * @var OutputFilter
   */
  static private $outputFilter = null;
  
  /**
   * 
   * @var Cache
   */
  static private $cache = null;
  
  /**
   * 
   * @var OptionAble
   *  
   */
  private $optionAble;
    
  private function setDebug($debug) {
    self::$debug = $debug;
  }
  
  public static function pocCallbackShowOutput($buffer) {
    $ret = $buffer;
    if (self::$debug) {
       $ret = $ret.'<br>This page has not been cached because the page as it is Blacklisted.'.
       ' <b> Was Generated within </b>'.
       '<b>'.((microtime() - self::$startTime) * 1000).'</b> milliseconds.';
    }
    self::$outputHandler->cacheCallback($ret);
    return $ret;
  }

  public static function pocCallbackGenerate($buffer) {
  	//TODO: call the ob_get_level from the outputHandler.
    if (self::$level == \ob_get_level() - 1) {
      if(self::$cache->getFilter()->evaluate())
      {
      	 $return = $buffer;
         if(!self::$outputFilter->isOutputBlacklisted($buffer)){
           if (self::$debug) {
              $return .= '<br>This page has been '
              .'<b> generated within </b> in '
              .'<b>'.((microtime() - self::$startTime) * 1000).
                                                        '</b> milliseconds.';
            }
            //TODO: add it to the OutputHandler.
            $headers = \headers_list();
            self::$headerManipulator->storeHeadersForPreservation($headers);
            self::$headerManipulator->removeHeaders($headers);
            //TODO: Hide the $key
            self::$cache->cacheSpecificStore(self::$cache->getHasher()->getKey(), $return);
            self::$headerManipulator->storeHeades($buffer);
            self::$cache->cacheAddTags();
         }

      }
      self::$outputHandler->cacheCallback($return);
    }
    return ($return);
  }

  public static function pocCallbackCache($buffer) {
    $return = $buffer;
    if (self::$debug) {
     $return .=  '<br>This page has been '
     .' <b> Fetched from the cache within </b>'
     .'<b>'.((microtime() - self::$startTime) * 1000).'</b> milliseconds.';
    }
    self::$outputHandler->cacheCallback($return);
    return $return;
  }

  public function fillDefaults(){
    $this->optionAble[self::PARAM_CACHE] = function (){return new FileCache();};
    $this->optionAble[self::PARAM_OUTPUTHANDLER] = function (){return new ServerOutput();};
    $this->optionAble[self::PARAM_HEADERMANIPULATOR] = function (){return new HeaderManipulator();};
    $this->optionAble[self::PARAM_OUTPUTFILTER] = function (){return new OutputFilter();};
    $this->optionAble[self::PARAM_DEBUG] = false;
  }
  
  
  /**
  @param PocCacheInterface $cache this placeholder class contains the various
  caches.
  @param bool $debug If true debug messages are provided in the output, only
  for develompment purposevags.
  */
  function __construct( $options = array() ) {
    $this->optionAble = new OptionAble($options, $this);
    $this->optionAble->start();
    self::$cache = $this->optionAble->getOption(self::PARAM_CACHE);
    self::$outputHandler = $this->optionAble->getOption(self::PARAM_OUTPUTHANDLER);
    self::$headerManipulator = $this->optionAble->getOption(self::PARAM_HEADERMANIPULATOR);
    self::$headerManipulator->setOutputHandler(self::$outputHandler);
    self::$headerManipulator->setCache(self::$cache);
    self::$outputFilter = $this->optionAble->getOption(self::PARAM_OUTPUTFILTER);
    $this->setDebug($this->optionAble->getOption('debug'));
  }

  private function fetchCache() {
   $started = 0;
      self::$cache->cacheTagsInvalidation();
      if (self::$cache->getFilter()->evaluate()) {
        //TODO: hide the key
        $output = self::$cache->fetch(self::$cache->getHasher()->getKey());
        if ($output) {
          self::$outputHandler->startBuffer('pocCallbackCache');
          self::$headerManipulator->fetchHeaders();
          //TODO:Replace it to it's appropriate place.(OutputHandler)
          $arr = headers_list();
          if (self::$headerManipulator->headersToSend) {
            foreach (self::$headerManipulator->headersToSend as $header) {
              self::$outputHandler->header($header);
            }
            self::$headerManipulator->removeHeaders($arr);
          }
          $started = 1;
          echo($output);
          self::$outputHandler->stopBuffer();
        }
      }
    //}
    return $started;
  }

  public function start() {

    self::$startTime = microtime();

    if (!$this->fetchCache()) {
      $startCache = true;
        if (self::$cache->getFilter()->isBlacklisted()) {
          $startCache = false;
        }
      if ($startCache) {
        self::$level = \ob_get_level();
        self::$outputHandler->startBuffer('pocCallbackGenerate');
      } else {
        self::$outputHandler->startBuffer('pocCallbackShowOutput');
      }
    }
  }

  public function __destruct() {
    if (isset(self::$level)) {
       if (self::$level) {
       	 self::$outputHandler->obEnd();
       }
    }
  }

  public function destruct() {
    $this->__destruct();
  }
}