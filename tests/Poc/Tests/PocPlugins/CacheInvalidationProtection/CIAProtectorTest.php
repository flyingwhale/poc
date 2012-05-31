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
use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\RediskaCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Cache\Tagging\MysqlTagging;

class CIAProtectorTest extends PocTestCore
{
    public function testCIAProtection ()
    {
        $outputHandler = new TestOutput();
        $cache = new FileCache();

        $cia = new CIAProtector();
        $cia->setCache($cache);
        $cnt = $cia->getSentinel();
        $cia->setSentinel(10);
        $cnt1 = $cia->getSentinel();


        $this->assertTrue($cnt == 0);
        $this->assertTrue($cnt1 == 10);

        $poc = new Poc(
                array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $outputHandler));

        $poc->addPlugin($cia);

        $poc->start();

        $this->pocBurner($poc, $outputHandler, rand());

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

