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

namespace Poc\Handlers\Callback;

use Poc\PocEvents\PocEventNames;
use Poc\Events\BaseEvent;
use Poc\Poc;


class CallbackHandler
{
    const CALLBACK_GENERATE = 'pocCallbackGenerate';
    const CALLBACK_SHOWOUTPUT = 'pocCallbackShowOutput';
    const CALLBACK_CACHE = 'pocCallbackCache';

    /**
     *
     * @var Poc
     */
    var $poc;

    /**
     *
     * @param Poc $poc
     */
    public function __construct(Poc $poc){
        $this->poc = $poc;
    }

    public function pocCallbackShowOutput ($buffer)
    {
        $this->poc->setOutput($buffer);
        if ($this->poc->getDebug()) {
            $this->poc->setOutput(
                $this->poc->getOutput() . '<br>This page has not been cached because the page is Blacklisted.' . ' <b> Was Generated in ' . ((microtime(true) - $this->startTime) * 1000) . '</b> milliseconds.');
        }

        $this->poc->getPocDispatcher()->dispatch(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,
                new BaseEvent($this->poc));
        $this->poc->getOutputHandler()->ObPrintCallback($buffer);

        return $this->poc->getOutput();
    }

    public function pocCallbackGenerate ($buffer)
    {
        $this->poc->setOutput($buffer);
        // TODO: call the ob_get_level from the outputHandler.
        if ($this->poc->getLevel() == \ob_get_level() - 1) {
            $this->poc->setOutput($buffer);
            $this->poc->getPocDispatcher()->dispatch(
            PocEventNames::BEFORE_THE_DECISION_IF_WE_CAN_STORE_THE_GENERATED_CONTENT,
                new BaseEvent($this->poc));
            if ($this->poc->getCanICacheThisGeneratedContent()) {
                if ($this->poc->getOutput()) {

                    if ($this->poc->getDebug()) {
                        $this->poc->setOutput(
                                $this->poc->getOutput() .
                                '<br>This page has been ' .
                                '<b> generated in ' .
                                ((microtime(true) - $this->poc->getStartTime()) * 1000) .
                                '</b> milliseconds.');
                    }
                    $headers = $this->poc->getOutputHandler()->headersList();

                    $this->poc->getHeaderManipulator()
                                        ->storeHeadersForPreservation($headers);

                    $this->poc->getHeaderManipulator()->removeHeaders($headers);

                    $this->poc->getPocDispatcher()->dispatch(
                      PocEventNames::BEFORE_STORE_OUTPUT, new BaseEvent($this->poc));

                    $this->poc->getCache()->cacheSpecificStore(
                            $this->poc->getHasher()->getKey(), $this->poc->getOutput());

                    $this->poc->getPocDispatcher()->dispatch(
                            PocEventNames::OUTPUT_STORED, new BaseEvent($this->poc));

                    $this->poc->getHeaderManipulator()->storeHeaders();

                    $this->poc->getPocDispatcher()->dispatch(
                            PocEventNames::HEADERS_STORED, new BaseEvent($this->poc));

                }
            } else {
                if ($this->poc->getDebug()) {
                    $this->poc->setOutput(
                            $this->poc->getOutput() . '<br>This page has been ' . '<b> generated in ' . ((microtime(true) - $this->poc->getStartTime()) * 1000) . '</b> milliseconds and is not cached because the outputfilter blacklisted it!');
                }
            }

            $this->poc->getPocDispatcher()->dispatch(
                    PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                    new BaseEvent($this->poc));

            if ($buffer) {
                $this->poc->getOutputHandler()->ObPrintCallback($this->poc->getOutput());

                return ($this->poc->getOutput());
            }
        }
    }

    public function pocCallbackCache ($buffer)
    {
        $this->poc->setOutput($buffer);
        if ($this->poc->getDebug()) {
            $this->poc->setOutput(
                    $this->poc->getOutput() . '<br>This page has been ' . ' <b> fetched from the cache in ' . ((microtime(true) - $this->poc->getStartTime()) * 1000) . '</b> milliseconds.');
        }
        $this->poc->getPocDispatcher()->dispatch(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                new BaseEvent($this->poc));
        $this->poc->getOutputHandler()->ObPrintCallback($this->poc->getOutput());

        return $this->poc->getOutput();
    }
}