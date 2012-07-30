<?php
namespace Poc\PocPlugins\HttpCache;

use Poc\PocEvents\PocEventNames;
use Poc\Poc;

use Poc\Events\BaseEvent;
use Poc\Core\PluginSystem\Plugin;

class Etag extends \Poc\Core\PluginSystem\Plugin
{

    const ETAG_POSTFIX = "_ET";
    public function init (Poc $poc)
    {
        parent::init($poc);
        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                       array($this, 'addEtag'));

        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINING,
                                                       array($this, 'checkEtag'));

        $poc->getPocDispatcher()->addListener(PocEventNames::HEADERS_STORED,
                                                     array($this, 'checkEtag'));

    }

    public function addEtag (BaseEvent $event)
    {
        $etag = md5($event->getEvent()->getOutput());
        $event->getEvent()->getCache()->cacheSpecificStore($event->getEvent()->getHasher()->getKey() . self::ETAG_POSTFIX, 1);
        $etagHeader = 'Etag: ' . $etag;
        $event->getEvent()->getHeaderManipulator()->headersToStore[] = $etagHeader;
        header($etagHeader);
    }

    public function checkEtag (BaseEvent $event)
    {
        $requestHeaders = getallheaders();
        $etag = $requestHeaders['If-None-Match'];
        if($event->getEvent()->getCache()->fetch($event->getEvent()->getHasher()->getKey() . self::ETAG_POSTFIX)){
            header("HTTP/1.0 304 Not Modified");
            header('Etag: ' . $etag);
            $event->getEvent()->getOutputHandler()->obEnd();
        }
    }

}
