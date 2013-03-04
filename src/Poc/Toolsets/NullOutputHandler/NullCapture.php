<?php
namespace Poc\Toolsets\NullOutputHandler;

//use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandler;
//
//use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\OutputInterface;

use Poc\Core\Events\BaseEvent;

//use Poc\Core\PocEvents\PocEventNames;
//
//use Poc\Poc;


use Poc\Toolsets\CaptureAbstract;

class NullCapture extends CaptureAbstract
{
    
    const PLUGIN_NAME = 'NULL';
 
    private function throwException()
    {
        throw new \Exception("Please add a capture interface to the POC!");
    }

    public function capture(BaseEvent $event)
    {
        $this->throwException();
    }

    public function getOutputFromCache(BaseEvent $event)
    {
        $this->throwException();
    }

    public function monitor(BaseEvent $event)
    {
        $this->throwException();
    }
     
    public function endOfBuffering (BaseEvent $event)
    {
        $this->throwException();
    }
}
