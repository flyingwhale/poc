<?php
namespace Poc;
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

/**
 * This Interface contains all the possible indexes for the parameters in the
 * Poc object.
 * Extends the CacheParams interface.
 *
 * @author Imer Toth
 *
 */
use Poc\Cache\CacheImplementation\CacheParams;

interface PocParams
{
    const PARAM_CACHE = 'PARAM_CACHE';

    const PARAM_HASHER = 'PARAM_HASHER';

    const PARAM_FILTER = 'PARAM_FILTER';

    const PARAM_TOOLSET = 'PARAM_TOOLSET';
}
