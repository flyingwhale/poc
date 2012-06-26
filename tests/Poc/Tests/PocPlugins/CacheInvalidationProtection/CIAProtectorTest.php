<?php
/*
 * Copyright 2011 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\Tests\PocPlugins\CacheInvalidationProtection;

use Poc\Tests\PocTestCore;

use Poc\PocPlugins\CacheInvalidationProtection\CIAProtector;
use Poc\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\FileCache;

class CIAProtectorTest extends PocTestCore
{
    public function testCIAProtection ()
    {
        $outputHandler = new TestOutput();
        $cache = new FileCache();

        $poc = new Poc(
                array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $outputHandler));

        $cia = new CIAProtector();
        $poc->addPlugin($cia);

        /*this 3 lines id for the tests only, in real life we don't do such things*/
        $cnt = $cia->getSentinel();
        $cia->setSentinel(10);
        $cnt1 = $cia->getSentinel();

        $this->assertTrue($cnt == 0);
        $this->assertTrue($cnt1 == 10);

        $poc->start();

        $this->pocBurner($poc, rand());

        $this->assertequals($cia->getRefreshPage(),$this->getOutput());

        /*
         * $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
         * Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_CIA_PROTECTOR
         * => new CIAProtector())); $this->pocBurner($poc, $outputHandler,
         * rand().rand()); $this->assertTrue($this->getOutput() !=
         * $cia->getRefreshPage());
         */
    }
}
