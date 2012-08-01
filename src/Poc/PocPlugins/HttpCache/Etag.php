<?php
/*
 * Copyright 2012 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

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
        $event->getEvent()->getCache()->cacheSpecificStore($event->getEvent()->getHasher()->getKey() . self::ETAG_POSTFIX, $etag);
        $etagHeader = 'Etag: ' . $etag;
        $event->getEvent()->getOutputHandler()->header($etagHeader);
    }

    public function checkEtag (BaseEvent $event)
    {
        $requestHeaders = $event->getEvent()->getOutputHandler()->getallheaders();
        if (isset($requestHeaders['If-None-Match']))
        {
            $etag = $requestHeaders['If-None-Match'];
            if($etag){
              $storedEtag = $event->getEvent()->getCache()->fetch($event->getEvent()->getHasher()->getKey() . self::ETAG_POSTFIX);

              if ($storedEtag == $etag ){
                  $event->getEvent()->getLogger()->setLog("inCheckEtag", $requestHeaders['If-None-Match']);
                  $event->getEvent()->getOutputHandler()->header('HTTP/1.0 304 Not Modified');
                  $event->getEvent()->getOutputHandler()->header('Etag: ' . $etag);
                  $event->getEvent()->getLogger()->setLog("ETAGGED", "Etag");
                  $event->getEvent()->getOutputHandler()->StopBuffer();
              }
            }
        }
    }

}
