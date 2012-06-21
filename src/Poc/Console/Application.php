<?php

/*
 * Copyright 2012 Imre Toth <tothimre at gmail>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Poc\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Poc\Console\Command\DbInitCommand;

class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Poc database initialization', 1);

        $this->add(new DbInitCommand());
        
    }

    public function getLongVersion()
    {
        return parent::getLongVersion();
    }
}
