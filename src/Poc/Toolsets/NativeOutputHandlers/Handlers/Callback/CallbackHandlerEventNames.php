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

interface CallbackHandlerEventNames
{
    const BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVOLVED = 'BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVOLVED';

    const BEFORE_THE_DECISION_IF_WE_CAN_STORE_THE_GENERATED_CONTENT = 'BEFORE_THE_DECISION_IF_WE_CAN_STORE_THE_GENERATED_CONTENT';

    const BEFORE_STORE_OUTPUT = 'BEFORE_STORE_OUTPUT';

    const COMPRESS_OUTPUT = 'COMPRESS_OUTPUT';

    const AFTER_COMPRESS_OUTPUT = 'AFTER_COMPRESS_OUTPUT';

    const OUTPUT_STORED = 'OUTPUT_STORED';

    const HEADERS_STORED = 'HEADERS_STORED';

    const BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED = 'BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED';

    const BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE = 'BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE';

}
