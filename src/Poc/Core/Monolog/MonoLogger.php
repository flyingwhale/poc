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
namespace Poc\Core\Monolog;

use Monolog\Handler\StreamHandler;

class MonoLogger implements LoggerInterface
{

    private $loggers;

    private $token;

    private $logFolder = '/tmp/';

    private $logPrefix = 'POC_LOG_';

    public function __construct ()
    {
        $this->token = md5(time() + rand());
    }

    public function setLog ($eventName, $output)
    {
        $this->getLogger($eventName)->addInfo($output);
    }

    public function getLogger ($eventName)
    {
        if (! isset($this->loggers[$eventName])) {
            $this->loggers[$eventName] = new PocLogger($this->token);
            $this->loggers[$eventName]->pushHandler(
                    new StreamHandler(
                            $this->logFolder . $this->logPrefix . 'POC_' . $eventName . '.log',
                            PocLogger::INFO));
        }

        return $this->loggers[$eventName];
    }

}
