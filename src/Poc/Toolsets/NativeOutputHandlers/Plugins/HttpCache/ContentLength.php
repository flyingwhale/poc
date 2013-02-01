<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
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

namespace Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Core\PocEvents\PocEventNames;
use Poc\Poc;

use Poc\Core\Events\BaseEvent;
use Poc\Core\PluginSystem\PluginInterface;

class ContentLength implements PluginInterface
{

    const LENGTH_POSTFIX = "_LN";
    public function init ($poc)
    {
        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                       array($this, 'calculateSize'));

        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                       array($this, 'printSize'));

        $poc->getPocDispatcher()->addListener(PocEventNames::HEADERS_STORED,
                                                     array($this, 'printSize'));

    }

    public function calculateSize (BaseEvent $event)
    {
        $size = strlen($event->getPoc()->getOutput());
        $event->getPoc()->getCache()->cacheSpecificStore($event->getPoc()->getHasher()->getKey() . self::LENGTH_POSTFIX, $size);
        $LengthHeader = 'Content-Length: ' . $size;
        $event->getPoc()->getOutputHandler()->header($LengthHeader);
    }

    public function printSize (BaseEvent $event)
    {
        $LengthHeader = $event->getPoc()->getCache()->fetch($event->getPoc()->getHasher()->getKey() . self::LENGTH_POSTFIX);
        $event->getPoc()->getOutputHandler()->header('Content-Length: ' . $LengthHeader);
    }
    
    public function getName() {
        return "HttpHeaderContentLength";
    }

}
