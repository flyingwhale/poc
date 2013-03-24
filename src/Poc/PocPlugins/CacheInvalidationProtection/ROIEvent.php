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

namespace Poc\PocPlugins\CacheInvalidationProtection;

use Symfony\Component\EventDispatcher\Event;

class ROIEvent extends Event
{

    /**
     *
     * @var ROIProtector
     */
    protected $cia;

    /**
     *
     * @return the $cia
     */
    public function getCia ()
    {
        return $this->cia;
    }

    public function __construct ($cia, $msg = '')
    {
        $this->cia = $cia;
        $this->msg = $msg;
    }

}
