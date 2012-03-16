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

use Poc\Plugins\PocLogsParams;

use Poc\Plugins\MinifyHtmlOutput;

use Poc\Plugins\PocLogs;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use Poc\Plugins\TestPlugin\Test2Plugin;

use Poc\PocEvents\PocEventNames;

use Poc\PocEvents\PocListener;

use Poc\Events\BaseEvent;

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



  /**
 * @return the $pocDispatcher
 */
  public function getPocDispatcher() {
    return $this->pocDispatcher;
  }

/**
 * @return the $startTime
 */
  public function getStartTime() {
    return $this->startTime;
  }

  private function setDebug($debug) {
    $this->debug = $debug;
  }


  public function pocCallbackShowOutput($buffer) {
    $this->setOutput($buffer);
    if ($this->debug) {
      $this->setOutput($this->getOutput().'<br>This page has not been cached because the page is Blacklisted.'.
       ' <b> Was Generated in '.
       ((microtime() - $this->startTime) * 1000).'</b> milliseconds.');
    }

    $this->pocDispatcher->dispatch(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,new BaseEvent($this));
    $this->outputHandler->ObPrintCallback($buffer);
    return $this->getOutput();
  }

  public function pocCallbackGenerate($buffer) {
    $this->setOutput($buffer);
    //TODO: call the ob_get_level from the outputHandler.
    if ($this->level == \ob_get_level() - 1) {
      if($this->cache->getFilter()->evaluate())
      {
      	 $this->setOutput($buffer);
         if(!$this->outputFilter->isOutputBlacklisted($this->getOutput())){
           if($this->getOutput()){

             if ($this->debug) {
                $this->setOutput($this->getOutput().'<br>This page has been '.
                '<b> generated in '.
                ((microtime() - $this->startTime) * 1000).
                '</b> milliseconds.');
              }
              $headers = $this->outputHandler->headersList();
              $this->headerManipulator->storeHeadersForPreservation($headers);
              $this->headerManipulator->removeHeaders($headers);
              $this->pocDispatcher->dispatch(PocEventNames::BEFORE_STORE_OUTPUT,new BaseEvent($this));
              //TODO: Hide the $key
              $this->cache->cacheSpecificStore($this->cache->getHasher()->getKey(), $this->getOutput());
              $this->headerManipulator->storeHeades($headers);
              $this->cache->cacheAddTags();

              if($this->ciaProtector){
                $this->ciaProtector->consultFinish();
             }
           }
         }
         else{
         	if ($this->debug) {
         		$this->setOutput($this->getOutput().'<br>This page has been '.
         				'<b> generated in '.
         				((microtime() - $this->startTime) * 1000).
         				'</b> milliseconds and is not cached because the outputfilter blacklisted it!');
         	}
         }

          $this->pocDispatcher->dispatch(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,new BaseEvent($this));

          if($buffer) {
         	$this->outputHandler->ObPrintCallback($this->getOutput());
         	return ($this->getOutput());
          }
      }
    }
  }

  public function pocCallbackCache($buffer) {
    $return = $buffer;
    $this->setOutput($buffer);
    if ($this->debug) {
     $this->setOutput($this->getOutput() .  '<br>This page has been '.
     ' <b> fetched from the cache in '.
     ((microtime() - $this->startTime) * 1000).'</b> milliseconds.');
    }
    $this->pocDispatcher->dispatch(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,new BaseEvent($this));
    $this->outputHandler->ObPrintCallback($this->getOutput());
    return $this->getOutput();
  }

  public function fillDefaults(){
    $this->optionAble[self::PARAM_CACHE] = function (){return new FileCache();};
    $this->optionAble[self::PARAM_OUTPUTHANDLER] = function (){return new ServerOutput();};
    $this->optionAble[self::PARAM_HEADERMANIPULATOR] = function (){return new HeaderManipulator();};
    $this->optionAble[self::PARAM_OUTPUTFILTER] = function (){return new OutputFilter();};
    $this->optionAble[self::PARAM_DEBUG] = false;
    $this->optionAble[self::PARAM_CIA_PROTECTOR] = function(){return new CIAProtector();};
    $this->optionAble[self::PARAM_EVENT_DISPATCHER] = function(){return new EventDispatcher();};
  }


  /**
  @param PocCacheInterface $cache this placeholder class contains the various
  caches.
  @param bool $debug If true debug messages are provided in the output, only
  for develompment purposevags.
  */
  function __construct( $options = array() ) {
    $this->startTime = microtime();
    $this->optionAble = new OptionAble($options, $this);
    $this->optionAble->start();
    $this->pocDispatcher = $this->optionAble->getOption(self::PARAM_EVENT_DISPATCHER);
    $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_BEGINING,new BaseEvent($this));
    //new PocLogs(array(PocLogsParams::PARAM_EVENT_DISPTCHER => $this->pocDispatcher));
    //new MinifyHtmlOutput($this->pocDispatcher);
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
    $this->pocDispatcher->dispatch(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,new BaseEvent($this));
    $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END,new BaseEvent($this));
  }

  private function fetchCache($ob_start = true) {
   $output = '';
      $this->cache->cacheTagsInvalidation();
      if ($this->cache->getFilter()->evaluate()) {
        //TODO: hide the key
        $output = $this->cache->fetch($this->cache->getHasher()->getKey());
        if ($output) {
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
    return $output;
  }

  public function start() {
    $this->level = \ob_get_level();

    $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END,new BaseEvent($this));
    if (!$this->fetchCache()) {
      if (!$this->cache->getFilter()->isBlacklisted()) {
        $this->checkCia();
        $this->outputHandler->startBuffer(self::CALLBACK_GENERATE);

        $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END,new BaseEvent($this));
      } else {
        $this->outputHandler->startBuffer(self::CALLBACK_SHOWOUTPUT);

        $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END,new BaseEvent($this));
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
    $this->pocDispatcher->dispatch(PocEventNames::DIES,new BaseEvent($this));
  }

  /**
   * @return the $output
   */
  public function getOutput() {
    return $this->output;
  }

  /**
   * @param string $output
   */
  public function setOutput($output) {
    $this->output = $output;
  }

  public function destruct() {
    $this->__destruct();
  }
}
