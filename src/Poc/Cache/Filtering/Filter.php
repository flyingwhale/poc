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

/**
 * This class helps you define when to cache a page and when not.
 * This class contains the blacklist / whitelist conditons and evaluates those.
 *
 * @author Imre Toth
 *
 */

namespace Poc\Cache\Filtering;

class Filter
{

    private $blacklistConditions = array();

    private $whitelistConditions = array();

    public function evaluate ()
    {
        return !$this->isBlacklisted();
    }

    public function addBlacklistCondition ($var)
    {
        $this->blacklistConditions[] = $var;
    }

    public function isBlacklisted ()
    {
        foreach ($this->blacklistConditions as $blackRequest) {
            if ($blackRequest) {
                return true;
            }
        }

        return false;
    }

}
