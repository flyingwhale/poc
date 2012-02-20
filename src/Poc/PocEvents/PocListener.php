<?php
namespace Poc\PocEvents;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use Poc\Core\Event\PocDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Poc\Poc;

class PocListener {

 
  //private $dispatcher;
  
  //function __construct(Poc $pocInstance){
  function __construct(){
    
    $this->dispatcher = PocDispatcher::getIstance();
    $this->dispatcher->addListener(PocEventNames::BEFORE_CACHED_OUTPUT_SENT, array($this, 'beforeSaveOutput'));
    $this->dispatcher->addListener(PocEventNames::BEFORE_CACHED_OUTPUT_SENT,function (PocEvent $e){  
      
      // create a log channel
      $log = new Logger('name');
      $log->pushHandler(new StreamHandler('/tmp/your.log', Logger::WARNING));
      
      // add records to the log
      $log->addWarning('Fooz');
      $log->addError('Barz');      
    });
    
  }
  
  function beforeSaveOutput(PocEvent $event){
    // create a log channel
      $log = new Logger('name');
      $log->pushHandler(new StreamHandler('/tmp/your.log', Logger::WARNING));
      
      // add records to the log
      $log->addWarning('Foo');
      $log->addError('Bar');
  }  
}
