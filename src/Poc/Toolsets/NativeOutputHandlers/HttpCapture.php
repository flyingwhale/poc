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
    private $callbackHandler;

    /**
     * This object stands for the output handling. I had to make
     * this abstraction because we whant testable code, and for the tests we
     * don't have the server environmnet, and we weeded to mock it somehow.
     * This is the solution for this problem.
     *
     * @var Handlers\Output\OutputInterface
     */
    private $outputHandler = null;

    public function init (Poc $poc)
    {
        parent::init($poc);

        $this->callbackHandler = new CallbackHandler($poc);
        $this->outputHandler = new ServerOutput();
        $this->outputHandler->setPoc($poc);

        $this->pocDispatcher->addListener(
                                           PocEventNames::GET_OUTPUT_FROM_CACHE,
                                            array($this, 'getOutputFromCache'));

        $this->pocDispatcher->addListener(
                                PocEventNames::FUNCTION_START_ENDS_CACHE_STARTS,
                                                        array($this, 'capture'));

    }

//     $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_GENERATE);

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

     /*
      *     $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_CACHE);
            //todo test it!
            $this->callbackHandler->getHeaderManipulator()->fetchHeaders();
            $this->outputHandler->stopBuffer($output);
      *
      */
}
