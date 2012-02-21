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
/**
 * This is tha main class this of the system this is the conductor of the 
 * system every functionalitys root if we inspect the flow of the application.
 * 
 * @author Imre Toth 
 * 
 */
namespace Poc;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use Poc\Plugins\TestPlugin\Test2Plugin;

use Poc\PocEvents\PocEventNames;

use Poc\PocEvents\PocListener;

use Poc\PocEvents\PocEvent;

use Poc\Core\Event\PocDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Poc\Core\Event\EventDictionary;

use Poc\Cache\CacheInvalidationProtection\CIAProtector;

use Poc\Core\OptionAble\OptionAbleInterface;

use Poc\Handlers\ServerOutput;

use Poc\Cache\CacheImplementation\FileCache;

use Poc\Core\OptionAble\OptionAble;

use Poc\Cache\CacheImplementation\AbstractPocCacheSpecific;

use Poc\Handlers\OutputInterface;

use Poc\Cache\Header\HeaderManipulator;

use Poc\Cache\Filtering\OutputFilter;

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
  const CALLBACK_GENERATE = 'pocCallbackGenerate';
  const CALLBACK_SHOWOUTPUT = 'pocCallbackShowOutput';
  const CALLBACK_CACHE = 'pocCallbackCache';

  /**
   *
   * @var OutputInterface
   */
  private $outputHandler = null;

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
  private $debug = null;

  /**
   * When the start function of the class executed sets its value by
   * calling the microtime function.
   *
   * @var unknown_type
   */
  private $startTime = null;

  /**
   *
   * @var unknown_type
   */
  private $level = null;

  /**
   *
   * @var HeaderManipulator
   */
  private $headerManipulator = null;

  /**
   *
   * @var OutputFilter
   */
  private $outputFilter = null;

  /**
   *
   * @var Cache
   */
  private $cache = null;

  /**
   *
   * @var OptionAble
   *
   */
  private $optionAble;

  /**
   *
   * @var CIAProtector
   */
  private $ciaProtector;
  
  /**
   * 
   * @var EventDictionary
   */
  private $eventDictionary;

  
  /**
   * 
   * @var \Symfony\Component\EventDispatcher\EventDispatcher;
   */
  private $pocDispatcher;

  private $pocListener;
  
  private function setDebug($debug) {
    $this->debug = $debug;
  }


  public function pocCallbackShowOutput($buffer) {
    $ret = $buffer;
    if ($this->debug) {
       $ret = $ret.'<br>This page has not been cached because the page is Blacklisted.'.
       ' <b> Was Generated in '.
       ((microtime() - $this->startTime) * 1000).'</b> milliseconds.';
    }
    $this->outputHandler->ObPrintCallback($buffer);
    
    return $ret;
  }

  public function pocCallbackGenerate($buffer) {
    //TODO: call the ob_get_level from the outputHandler.
    if ($this->level == \ob_get_level() - 1) {
      if($this->cache->getFilter()->evaluate())
      {
      	 $return = $buffer;
         if(!$this->outputFilter->isOutputBlacklisted($buffer)){
           if($buffer){       
                
             if ($this->debug) {
                $return .= '<br>This page has been '.
                '<b> generated in '.
                ((microtime() - $this->startTime) * 1000).
                '</b> milliseconds.';
              }
              $headers = $this->outputHandler->headersList();
              $this->headerManipulator->storeHeadersForPreservation($headers);
              $this->headerManipulator->removeHeaders($headers);
              //TODO: Hide the $key
              $this->cache->cacheSpecificStore($this->cache->getHasher()->getKey(), $return);
              $this->headerManipulator->storeHeades($headers);
              $this->cache->cacheAddTags();
              
              if($this->ciaProtector){
                $this->ciaProtector->consultFinish();
             }
           }
         }
          
          $this->pocDispatcher->dispatch(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT,new PocEvent($this));
                  
          if($buffer) {
         	$this->outputHandler->ObPrintCallback($buffer);
         	return ($return);
          }
      }
    }
  }

  public function pocCallbackCache($buffer) {
    $return = $buffer;
    if ($this->debug) {
     $return .=  '<br>This page has been '.
     ' <b> fetched from the cache in '.
     ((microtime() - $this->startTime) * 1000).'</b> milliseconds.';
    }
    $this->outputHandler->ObPrintCallback($return);
    return $return;
  }

  public function fillDefaults(){
    $this->optionAble[self::PARAM_CACHE] = function (){return new FileCache();};
    $this->optionAble[self::PARAM_OUTPUTHANDLER] = function (){return new ServerOutput();};
    $this->optionAble[self::PARAM_HEADERMANIPULATOR] = function (){return new HeaderManipulator();};
    $this->optionAble[self::PARAM_OUTPUTFILTER] = function (){return new OutputFilter();};
    $this->optionAble[self::PARAM_DEBUG] = false;
    $this->optionAble[self::PARAM_CIA_PROTECTOR] = function(){return new CIAProtector();};
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
    $this->cache = $this->optionAble->getOption(self::PARAM_CACHE);
    $this->outputHandler = $this->optionAble->getOption(self::PARAM_OUTPUTHANDLER);
    $this->outputHandler->setPoc($this);
    $this->headerManipulator = $this->optionAble->getOption(self::PARAM_HEADERMANIPULATOR);
    $this->headerManipulator->setOutputHandler($this->outputHandler);
    $this->headerManipulator->setCache($this->cache);
    $this->outputFilter = $this->optionAble->getOption(self::PARAM_OUTPUTFILTER);
    $this->ciaProtector = $this->optionAble->getOption(self::PARAM_CIA_PROTECTOR);
    $this->ciaProtector->setCache($this->cache);
    $this->ciaProtector->setOutputHandler($this->outputHandler);    
    $this->setDebug($this->optionAble->getOption('debug'));
    $this->pocDispatcher = PocDispatcher::getIstance();
  }

  private function fetchCache($ob_start = true) {
   $output = '';
      $this->cache->cacheTagsInvalidation();
      if ($this->cache->getFilter()->evaluate()) {
        //TODO: hide the key
        $output = $this->cache->fetch($this->cache->getHasher()->getKey());
        if ($output) {
          if($ob_start){
            $this->outputHandler->startBuffer(self::CALLBACK_CACHE);
            $this->headerManipulator->fetchHeaders();
            //TODO:Replace it to it's appropriate place.(OutputHandler)
            $arr = headers_list();
            if ($this->headerManipulator->headersToSend) {
              foreach ($this->headerManipulator->headersToSend as $header) {
                $this->outputHandler->header($header);
              }
              $this->headerManipulator->removeHeaders($arr);
            }
            $started = 1;
            echo($output);
            $this->outputHandler->stopBuffer();
          }
        }
      }
    //}
    return $output;
  }

  public function start() {
    $this->startTime = microtime();
    $this->level = \ob_get_level();
    if (!$this->fetchCache()) {
      if (!$this->cache->getFilter()->isBlacklisted()) {
        $this->checkCia();
        $this->outputHandler->startBuffer(self::CALLBACK_GENERATE);
      } else {
        $this->outputHandler->startBuffer(self::CALLBACK_SHOWOUTPUT);
      }
    }
  }

  private function checkCia (){
    if($this->ciaProtector){
      $this->ciaProtector->consult();  
    }
  }
  
  public function __destruct() {
    if (isset($this->level)) {
      if ($this->level) {
       	 $this->outputHandler->obEnd();
       }
    }
  }

  public function destruct() {
    $this->__destruct();
  }
}
