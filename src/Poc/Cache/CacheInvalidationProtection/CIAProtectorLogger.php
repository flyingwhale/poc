<?php
namespace Poc\Cache\CacheInvalidationProtection;

use Poc\PocPlugins\PocLogsParams;

use Poc\PocParams;

use Poc\Core\OptionAble\OptionAble;

use Poc\Core\OptionAble\OptionAbleInterface;

use Poc\Core\Event\PocDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Poc\Poc;

class CIAProtectorLogger implements OptionAbleInterface, CIAProtectorEventNames{

  /**
   *
   * @var EventDispatcher
   */
  private $pocDispatcher;

  /**
   *
   * @var OptionAble
   *
   */
  private $optionAble;

  /**
   *
   * @var Poc;
   */
  private $poc;
  /**
   *
   * @var LoggerInterface
   */
  private $logger;

  public function fillDefaults(){
    $this->optionAble[PocLogsParams::PARAM_POC] = null;
  }

  function __construct($options = array()){

    $this->optionAble = new OptionAble($options, $this);
    $this->optionAble->start();

    $this->poc = $this->optionAble->getOption(PocLogsParams::PARAM_POC);
    $this->logger = $this->poc->getLogger();

    $this->pocDispatcher = $this->poc->getPocDispatcher();

    $this->pocDispatcher->addListener(CIAProtectorEventNames::CONSULT_STARTED,
                                      array($this, 'consultLogger'));

    $this->pocDispatcher->addListener(CIAProtectorEventNames::CONSULT_STARTED_NOT_FIRST,
                                      array($this, 'consultFirstLogger'));

    $this->pocDispatcher->addListener(CIAProtectorEventNames::CONSULT_STARTED_FIRST,
                                      array($this, 'consultNotFirstLogger'));

    $this->pocDispatcher->addListener(CIAProtectorEventNames::CONSULT_SLEEP,
    		array($this, 'consultSleep'));

    //'I am sleeping '.$sentinelCnt);
  }


  function consultLogger(CiaEvent $event){
    $this->logger->setLog(CIAProtector::LOG_TYPE_CIA, 'sentiel after inc cnt: '. $event->getCia()->getSentinel());
  }
  function consultFirstLogger(CiaEvent $event){
  	$this->logger->setLog(CIAProtector::LOG_TYPE_CIA, 'FIRST');
  }
  function consultNotFirstLogger(CiaEvent $event){
  	$this->logger->setLog(CIAProtector::LOG_TYPE_CIA, 'NOT FIRST');
  }
  function consultSleep(CiaEvent $event){
  	$this->logger->setLog(CIAProtector::LOG_TYPE_CIA, 'I am sleeping: '.$event->getCia()->getSentinel());
  }

}

