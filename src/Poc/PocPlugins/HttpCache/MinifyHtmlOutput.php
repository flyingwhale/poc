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
