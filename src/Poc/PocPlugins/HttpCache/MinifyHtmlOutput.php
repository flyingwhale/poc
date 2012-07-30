<?php
namespace Poc\PocPlugins\HttpCache;

use Poc\PocEvents\PocEventNames;
use Poc\Poc;

use Poc\Events\BaseEvent;
use Poc\Core\PluginSystem\Plugin;

class Etag extends \Poc\Core\PluginSystem\Plugin
{

    const ETAG_PREFIX = "ET_";
    public function init (Poc $poc)
    {
        parent::init($poc);
        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                       array($this, 'addEtag'));
    }

    public function addEtag (BaseEvent $event)
    {
        $etag = md5($event->getEvent()->getOutput());
        $event->getEvent()->getCache()->cacheSpecificStore(self::ETAG_PREFIX.$etag, 1);
        $event->getEvent()->getHeaderManipulator()->headersToStore[] = 'Etag : ' . $etag;
    }

    public function checkEtag (BaseEvent $event)
    {
    }
}
