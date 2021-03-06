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

namespace Poc\Toolsets\NativeOutputHandlers\Handlers\Output;

use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandler;

use Poc\Poc;

abstract class Output implements OutputInterface
{

     /**
     *
     * @var CallbackHandler
     */
     protected $callbackHandler;

    /**
     *
     * @var Poc
     */
    protected $poc = null;

    public function setPoc (Poc $poc)
    {
        $this->poc = $poc;
    }

    /**
     *
     * @param CallbackHandler $callbackHandler
     */
    public function setCallbackHandler(CallbackHandler $callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
    }

}
