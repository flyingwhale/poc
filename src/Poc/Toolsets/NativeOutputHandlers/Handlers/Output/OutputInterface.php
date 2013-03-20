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

/**
 * This Interface Defines the set of functions that has to be defined by the
 * classes that handles the output interface for the POC.
 * This is necesarry to
 * have for proper unittests becausse the output has to be handled differently
 * in the CLI environment compared to a Server Environment.
 *
 * @author Imre Toth
 *
 */

namespace Poc\Toolsets\NativeOutputHandlers\Handlers\Output;

use Poc\Poc;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandler;

interface OutputInterface
{
    public function getLevel ();

    public function startBuffer ($callbackFunctname);

    public function stopBuffer ($output = '');

    public function header ($header);

    public function obEnd ();

    public function ObPrintCallback ($output);

    public function headersList ();

    public function getallheaders();

    /**
     *
     * @param $poc Poc
     */
    public function setPoc (Poc $poc);
    
    /**
     * 
     * @param CallbackHandler $callbackHandler
     */
    public function setCallbackHandler(CallbackHandler $callbackHandler);
}
