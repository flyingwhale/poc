<?php
/*
 * Copyright 2013 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Core\PocEvents\PocEventNames;
use Poc\Poc;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

use Poc\Core\Events\BaseEvent;
use FlyingWhale\Plugin\PluginInterface;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\Events\EtagEvents;

class Etag implements PluginInterface
{

    const ETAG_POSTFIX = "_ET";

    public function pluginInit ($poc)
    {
        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                       array($this, 'addEtag'));

        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                       array($this, 'checkEtag'));

        $poc->getPocDispatcher()->addListener(PocEventNames::HEADERS_STORED,
                                                     array($this, 'checkEtag'));
    }

    public function isPluginMultipleInstanced()
    {
        return false;
    }

    public function addEtag (BaseEvent $event)
    {
        $etag = md5($event->getPoc()->getOutput());
        $event->getPoc()->getCache()->
                cacheSpecificStore($event->getPoc()->getHasher()
                                        ->getKey() . self::ETAG_POSTFIX, $etag);

        $etagHeader = 'Etag: ' . $etag;
        $event->getPoc()->getPluginRegistry()
                ->getPlugin(HttpCapture::PLUGIN_NAME)->
                                        getOutputHandler()->header($etagHeader);
    }

    public function checkEtag (BaseEvent $event)
    {
        $requestHeaders = $event->getPoc()->getPluginRegistry()->getPlugin(HttpCapture::PLUGIN_NAME)->
                                            getOutputHandler()->getallheaders();
        if (isset($requestHeaders['If-None-Match'])) {
            $etag = $requestHeaders['If-None-Match'];
            if ($etag) {
              $storedEtag = $event->getPoc()->getCache()->fetch($event->getPoc()->getHasher()->getKey() . self::ETAG_POSTFIX);

              if ($storedEtag == $etag) {
                  $event->getPoc()->getPocDispatcher()->dispatch(EtagEvents::ETAG_FOUND, new BaseEvent($event->getPoc()));
                  $outputHandler = $event->getPoc()->getPluginRegistry()->
                        getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();
                  $outputHandler->header('HTTP/1.0 304 Not Modified');
                  $outputHandler->header('Etag: ' . $etag);
                  //$event->getPoc()->getOutputHandler()->StopBuffer();
              } else {
                  $event->getPoc()->getPocDispatcher()->dispatch
                  (EtagEvents::ETAG_NOT_FOUND, new BaseEvent($event->getPoc()));
              }
            }
        }
    }

    public function getPluginName()
    {
        return "HttpEtag";
    }

}
