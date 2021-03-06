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

namespace Poc\Toolsets\NativeOutputHandlers\Handlers\Callback;

use Poc\Core\Events\BaseEvent;
use Poc\Poc;
use Poc\Toolsets\NativeOutputHandlers\Header\HeaderManipulator;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class CallbackHandler
{
    const CALLBACK_GENERATE = 'pocCallbackGenerate';
    const CALLBACK_SHOWOUTPUT = 'pocCallbackShowOutput';
    const CALLBACK_CACHE = 'pocCallbackCache';

    /**
     *
     * @var Poc
     */
    public $poc;

    /**
     *
     * @var HeaderManipulator
     */
    private $headerManipulator;

    /**
     *
     * @var \Poc\Toolsets\NativeOutputHandlers\Handlers\Output\OutputInterface
     */
    private $outputHandler;

    /**
     *
     * @var HttpCapture;
     */
    private $httpCapture;

    /**
     *
     * @param Poc $poc
     */
    public function __construct(Poc $poc)
    {
        $this->poc = $poc;
        $this->headerManipulator = new HeaderManipulator();
        $this->headerManipulator->setPoc($poc);
        $this->outputHandler = $poc->getEventDispatcher()->getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();
        $this->httpCapture = $poc->getEventDispatcher()->getPlugin(HttpCapture::PLUGIN_NAME);
    }

    public function getHeaderManipulator()
    {
        return $this->headerManipulator;
    }

    public function pocCallbackShowOutput ($buffer)
    {
        $this->poc->setOutput($buffer);

        $this->poc->getPocDispatcher()->dispatch(
                                    CallbackHandlerEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVOLVED,
                                                                                             new BaseEvent($this->poc));

        $this->poc->getPocDispatcher()->dispatch(
                CallbackHandlerEventNames::COMPRESS_OUTPUT,
                new BaseEvent($this->poc));

        $this->outputHandler->ObPrintCallback($buffer);

        return $this->poc->getOutput();
    }

    public function pocCallbackGenerate ($buffer)
    {
        $this->poc->setOutput($buffer);
        if ($this->httpCapture->getLevel() == $this->outputHandler->getLevel() - 1) {
            $this->poc->getPocDispatcher()->dispatch(
                                   CallbackHandlerEventNames::BEFORE_THE_DECISION_IF_WE_CAN_STORE_THE_GENERATED_CONTENT,
                                                                                             new BaseEvent($this->poc));
            if ($this->poc->getCanICacheThisGeneratedContent()) {
                if ($this->poc->getOutput()) {
                    $headers = $this->outputHandler->headersList();

                    //Headers stored here.
                    $this->headerManipulator->storeHeadersForPreservation($headers);

                    //Remove unneeded headers.
                    $this->headerManipulator->removeHeaders();

                    $this->poc->getPocDispatcher()->dispatch(CallbackHandlerEventNames::BEFORE_STORE_OUTPUT,
                                                                                             new BaseEvent($this->poc));

                    $this->poc->getPocDispatcher()->dispatch(CallbackHandlerEventNames::COMPRESS_OUTPUT,
                                                                                             new BaseEvent($this->poc));

                    $this->poc->getPocDispatcher()->dispatch(CallbackHandlerEventNames::AFTER_COMPRESS_OUTPUT,
                                                                                             new BaseEvent($this->poc));

                    $this->poc->getCache()->cacheSpecificStore($this->poc->getHasher()->getKey(),
                                                                                               $this->poc->getOutput());

                    $this->poc->getPocDispatcher()->dispatch(CallbackHandlerEventNames::OUTPUT_STORED,
                                                                                             new BaseEvent($this->poc));

                    $this->headerManipulator->storeHeaders();

                    $this->poc->getPocDispatcher()->dispatch(CallbackHandlerEventNames::HEADERS_STORED,
                                                                                             new BaseEvent($this->poc));

                }
            }

            $this->poc->getPocDispatcher()->dispatch(
                                            CallbackHandlerEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                                                                                             new BaseEvent($this->poc));

            if ($buffer) {
                $this->outputHandler->ObPrintCallback($this->poc->getOutput());

                return ($this->poc->getOutput());
            }
        }
    }

    public function pocCallbackCache ($buffer)
    {
        $this->poc->setOutput($buffer);
        $this->poc->getPocDispatcher()->dispatch(
                                             CallbackHandlerEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                                                                                             new BaseEvent($this->poc));
        $this->outputHandler->ObPrintCallback($this->poc->getOutput());

        return $this->poc->getOutput();
    }
}
