<?php
namespace Poc\Toolsets;

use Poc\Core\PluginSystem\PluginInterface;

use Poc\Core\Events\BaseEvent;

interface CaptureIntrerface extends PluginInterface
{
    public function capture(BaseEvent $event);

    public function getOutputFromCache(BaseEvent $event);

    public function monitor(BaseEvent $event);
     
    public function endOfBuffering (BaseEvent $event);
}
