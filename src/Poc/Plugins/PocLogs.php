<?php
namespace Poc\Plugins;

use Poc\PocEvents\PocEvent;

use Poc\PocEvents\PocEventNames;

use Poc\Core\OptionAble\OptionAble;

use Poc\Core\OptionAble\OptionAbleInterface;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use Poc\Core\Event\PocDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Poc\Poc;

class PocLogs implements OptionAbleInterface, PocLogsParams{
  
  const LOG_TYPE_OUTPUT = "_OUTPUT";
  const LOG_TYPE_TIME = "_TIME";
  
  private $logFolder;
  private $logPrefix;
  /**
   * 
   * @var Event
   */
  private $pocDispatcher;
  
  private $loggers;

  /**
   *
   * @var OptionAble
   *
   */
  private $optionAble;

  private $token;
  
  public function fillDefaults(){
    $this->optionAble[self::PARAM_TMP_FOLDER] = "/tmp/";
    $this->optionAble[self::PARAM_LOG_PREFIX] = "POC_LOG_";
    $this->optionAble[self::PARAM_EVENT_DISPTCHER] = null;
  }

  function __construct($options = array()){
    $this->token = md5(time()+rand());
    
    $this->optionAble = new OptionAble($options, $this);
    $this->optionAble->start();
    
    $this->logFolder = $this->optionAble->getOption(self::PARAM_TMP_FOLDER);
    $this->logPrefix = $this->optionAble->getOption(self::PARAM_LOG_PREFIX);
    $this->pocDispatcher = $this->optionAble->getOption(self::PARAM_EVENT_DISPTCHER);
    
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED, 
                                      array($this, 'beforeOutputSentToClinetAfterOutputStoredTime'));
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED, 
                                      array($this, 'beforeOutputSentToClinetAfterOutputStoredOutput'));
    
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED, 
                                      array($this, 'beforeOutputSentToClientNoCachingProcessInvolvedTime'));
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED, 
                                      array($this, 'beforeOutputSentToClientNoCachingProcessInvolvedOutput'));

    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE, 
                                      array($this, 'beforeOutputSentToClientFetchedFromCacheTime'));
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE, 
                                      array($this, 'beforeOutputSentToClientFetchedFromCacheOutput'));

    $this->pocDispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT, 
                                      array($this, 'beforeStoreOutputTime'));
    $this->pocDispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT, 
                                      array($this, 'beforeStoreOutputOutput'));

  }
   
  function beforeOutputSentToClinetAfterOutputStoredTime(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,self::LOG_TYPE_TIME);
  } 
  function beforeOutputSentToClinetAfterOutputStoredOutput(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,self::LOG_TYPE_OUTPUT);    
  }
  
  function beforeOutputSentToClientNoCachingProcessInvolvedTime(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,self::LOG_TYPE_TIME);
  }
  function beforeOutputSentToClientNoCachingProcessInvolvedOutput(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,self::LOG_TYPE_OUTPUT);
  }
  
  function beforeOutputSentToClientFetchedFromCacheTime(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,self::LOG_TYPE_TIME);
  }
  function beforeOutputSentToClientFetchedFromCacheOutput(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,self::LOG_TYPE_OUTPUT);
  }
  
  function beforeStoreOutputTime(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_STORE_OUTPUT,self::LOG_TYPE_TIME);
  }
  function beforeStoreOutputOutput(PocEvent $event){
    $this->logTime($event, PocEventNames::BEFORE_STORE_OUTPUT,self::LOG_TYPE_OUTPUT);
  }
  
  private function logOutput(PocEvent $event, $eventName, $type){
    $this->log($eventName, $event->getPoc()->getOutput(),$type);
  }
  
  private function logTime(PocEvent $event, $eventName, $type){
    $this->log($eventName,\microtime()-$event->getPoc()->getStartTime(),$type);
  }
  
  private function log($eventName, $saveIt, $type){
    $this->setLog($eventName)->addInfo($saveIt);
    $this->setLog($type)->addInfo($saveIt);
    $this->setLog($eventName.$type)->addInfo($saveIt);
  }
  
  /**
   * 
   * @param unknown_type $eventName
   * @return \Monolog\Logger
   */
  private function setLog($eventName){
    $log = $this->getLogger($eventName);
    return $log;
  }
  
  /**
   * 
   * @param string $eventName
   * @return Logger
   */
  private function getLogger($eventName){
    if (!isset($this->loggers[$eventName])){
      $this->loggers[$eventName] = new Logger($this->token);
      $this->loggers[$eventName]->pushHandler(new StreamHandler($this->logFolder.$this->logPrefix.$eventName.'.log', Logger::INFO));
    }
    return $this->loggers[$eventName];
  }
  
  public function __destruct(){
    unset($this->loggers);
  }
    
}

