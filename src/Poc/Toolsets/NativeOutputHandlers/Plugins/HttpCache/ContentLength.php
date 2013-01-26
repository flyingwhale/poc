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
use Poc\Core\PluginSystem\Plugin;

class ContentLength extends \Poc\Core\PluginSystem\Plugin
{

    const LENGTH_POSTFIX = "_LN";
    public function init (Poc $poc)
    {
        parent::init($poc);
        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                       array($this, 'calculateSize'));

        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                       array($this, 'printSize'));

        $poc->getPocDispatcher()->addListener(PocEventNames::HEADERS_STORED,
                                                     array($this, 'printSize'));

    }

    public function calculateSize (BaseEvent $event)
    {
        $size = strlen($event->getEvent()->getOutput());
        $event->getEvent()->getCache()->cacheSpecificStore($event->getEvent()->getHasher()->getKey() . self::LENGTH_POSTFIX, $size);
        $LengthHeader = 'Content-Length: ' . $size;
        $event->getEvent()->getOutputHandler()->header($LengthHeader);
    }

    public function printSize (BaseEvent $event)
    {
        $LengthHeader = $event->getEvent()->getCache()->fetch($event->getEvent()->getHasher()->getKey() . self::LENGTH_POSTFIX);
        $event->getEvent()->getOutputHandler()->header('Content-Length: ' . $LengthHeader);
    }

}
