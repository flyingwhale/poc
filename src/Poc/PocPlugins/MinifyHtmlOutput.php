<?php
namespace Poc\PocPlugins;

use Poc\PocEvents\PocEventNames;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Events\BaseEvent;

class MinifyHtmlOutput {

  /**
   * 
   * @var PocDispatcher
   */
  private $dispatcher;
  
  function __construct(EventDispatcher $dispatcher){
    $dispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT, array($this, 'minifyHtml'));
  }

  function minifyHtml(BaseEvent $event){
    //got from php.net
    $search = array(
        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s'  // shorten multiple whitespace sequences
    );
    $replace = array(
        '>',
        '<',
        '\\1'
    );
    //die(get_class($event));
    $event->getEvent()->setOutput(preg_replace($search, $replace, $event->getEvent()->getOutput()));
  }
  
  
}
