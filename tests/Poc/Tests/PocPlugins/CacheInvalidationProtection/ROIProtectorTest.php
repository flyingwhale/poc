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

namespace Poc\Tests\PocPlugins\CacheInvalidationProtection;

use Poc\Tests\NativeOutputHandlersTestCore;

use Poc\PocPlugins\CacheInvalidationProtection\ROIProtector;
use Poc\Poc;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;

class ROIProtectorTest extends NativeOutputHandlersTestCore
{
    const BIG_SENTIEL_VALUE = 10;

    /**
     *
     * @var \Pimple
     */
    private $pocContainer;

    /**
     *
     * @var ROIProtector
     */
    private $cia;

    private $nativeOutputHandler;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->nativeOutputHandler = new NativeOutputHandlersTestCore();

        $this->pocContainer = new \Pimple;
        $this->pocContainer['poc'] = function () {
            $cache = new FileCache();
            $hasher = new Hasher();
            $hasher->addDistinguishVariable(rand());
            $poc = new Poc(
                    array(Poc::PARAM_CACHE => $cache,
                          Poc::PARAM_TOOLSET =>
                                            new HttpCapture(new TestOutput())));

            return $poc;
        };

        $this->pocContainer['noh'] = function () {
            return new NativeOutputHandlersTestCore();
        };

        $this->pocContainer['cia'] = function () {
            return new ROIProtector();
        };

        $this->cia = new ROIProtector;
    }

    // todo: Add more relevant tests!
    public function testROIProtectionResettingSentinel ()
    {

        $poc2 = $this->pocContainer['poc'];
        $cia2 = $this->pocContainer['cia'];
        $rnd = rand();
        $poc2->addPlugin($cia2);
        $cia2->setSentinel(1);
        $noh2 = $this->pocContainer['noh'];
        $noh2->pocBurner($poc2, $rnd);

        $this->assertNotEquals($cia2->getRefreshPage(), $this->getOutput());
        $this->assertEquals($cia2->getSentinel(), 0);

        //todo: check why it is not working!!
        //$this->assertequals($rnd, $noh2->getOutput());

    }

    public function testROIProtectionIncreasingSentinelAndShowingRefreshPage ()
    {
        $poc1 = $this->pocContainer['poc'];
        $cia = $this->pocContainer['cia'];

        $poc1->addPlugin($cia);

        //this 3 lines id for the tests only, in real life we don't do such things

        $cnt = $cia->getSentinel();
        $cia->setSentinel(self::BIG_SENTIEL_VALUE);
        $cnt1 = $cia->getSentinel();

        $this->assertEquals($cnt, 0);
        $this->assertEquals($cnt1, self::BIG_SENTIEL_VALUE);

        $noh = $this->pocContainer['noh'];
        $noh->pocBurner($poc1, rand());
        $this->assertequals($this->cia->getRefreshPage(),$noh->getOutput());
        $cnt2 = $cia->getSentinel();
        $this->assertEquals($cnt2, self::BIG_SENTIEL_VALUE + 1);

        /*
         * $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
         * Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_CIA_PROTECTOR
         * => new CIAProtector())); $this->pocBurner($poc, $outputHandler,
         * rand().rand()); $this->assertTrue($this->getOutput() !=
         * $cia->getRefreshPage());
         */
    }
}
