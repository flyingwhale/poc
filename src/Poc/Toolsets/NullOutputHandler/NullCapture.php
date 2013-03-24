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

    public function __construct()
    {
        $this->throwException();
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
