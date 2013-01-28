<?php
namespace Poc\Toolsets\NativeOutputHandlers;

use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandler;

use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\ServerOutput;

use Poc\Core\Events\BaseEvent;

use Poc\Core\PluginSystem\Plugin;

use Poc\Core\PocEvents\PocEventNames;

use Poc\Poc;

class HttpCapture extends Plugin
{
    /**
     *
     * @var Handlers\Callback\CallbackHandler
     */
    private $callbackHandler = null;

    /**
     * This object stands for the output handling. I had to make
     * this abstraction because we whant testable code, and for the tests we
     * don't have the server environmnet, and we weeded to mock it somehow.
     * This is the solution for this problem.
     *
     * @var Handlers\Output\OutputInterface
     */
    private $outputHandler = null;
    
    private $level;

    public function setLevel($value)
    {
        $this->level = $value;
    }

    public function getLevel($value)
    {
        return $this->level;
    }

    public function init (Poc $poc)
    {
        parent::init($poc);

        $this->callbackHandler = new CallbackHandler($poc);
        
        $this->outputHandler =  $poc->getOutputHandler();
        $this->outputHandler->setCallbackHandler($this->callbackHandler);
        
        $this->outputHandler->setPoc($poc);

        $this->pocDispatcher->addListener(
                                           PocEventNames::GET_OUTPUT_FROM_CACHE,
                                            array($this, 'getOutputFromCache'));

        $this->pocDispatcher->addListener(
                                PocEventNames::CAPTURE,array($this, 'capture'));

        $this->pocDispatcher->addListener(
                                PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                    array($this, 'setObLevel'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::MONITOR,
                                                    array($this, 'monitor'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::END_OF_BUFFERING,
                                                array($this, 'endOfBuffering'));
        
    }


     public function setObLevel(BaseEvent $event)
     {
         $this->level = $this->outputHandler->getLevel();
     }

     public function capture(BaseEvent $event)
     {
         $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_GENERATE);
     }

     public function getOutputFromCache(BaseEvent $event)
     {
         $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_CACHE);
         //todo test it!
         $this->callbackHandler->getHeaderManipulator()->fetchHeaders();
         $this->outputHandler->stopBuffer($this->poc->getOutput());
     }

     public function monitor(BaseEvent $event)
     {
        $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_SHOWOUTPUT);
     }
     
     public function endOfBuffering (BaseEvent $event)
     {
        if (isset($this->level)) {
            if ($this->level) {
                $this->outputHandler->obEnd();
            }
        }         
    }

    public function setName() {
        $this->name = "HttpCapture";
    }
}
