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

use Poc\PocPlugins\CacheInvalidationProtection\ROIProtector;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;

class ROIProtectorTest extends PocTestCore
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
    
    
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);
        
        $this->pocContainer = new \Pimple;
        $this->pocContainer['poc'] = function ()
        {
            $outputHandler = new TestOutput();
            $cache = new FileCache();
            $hasher = new Hasher();
            $hasher->addDistinguishVariable(rand());
            $poc = new Poc(
                    array(Poc::PARAM_CACHE => $cache, Poc::PARAM_OUTPUTHANDLER => $outputHandler));

            return $poc;
        };
        
        $this->cia = new ROIProtector();

        
    }


    // todo: Add more relevant tests!
    public function testROIProtection ()
    {

        $poc1 = $this->pocContainer['poc'];
        
        $poc1->addPlugin($this->cia);

        /*this 3 lines id for the tests only, in real life we don't do such things*/
        $cnt = $this->cia->getSentinel();
        $this->cia->setSentinel(self::BIG_SENTIEL_VALUE);
        $cnt1 = $this->cia->getSentinel();

        $this->assertEquals($cnt, 0);
        $this->assertEquals($cnt1, self::BIG_SENTIEL_VALUE);

        $this->pocBurner($poc1, rand());

        $this->assertequals($this->cia->getRefreshPage(),$this->getOutput());

        $cnt2 = $this->cia->getSentinel();
        $this->assertEquals($cnt2, self::BIG_SENTIEL_VALUE + 1);
        
        $this->cia->setSentinel(1);
        
        $rnd = rand();

        $poc2 = $this->pocContainer['poc'];
        
        $poc2->addPlugin($this->cia);
        
        $this->pocBurner($poc2, $rnd);
        $this->assertNotEquals($this->cia->getRefreshPage(), $this->getOutput());
        $this->assertEquals($this->cia->getSentinel(), 0);        
        $this->assertequals($rnd, $this->getOutput());
        
        
        /*
         * $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
         * Poc::PARAM_OUTPUTHANDLER => $outputHandler, Poc::PARAM_CIA_PROTECTOR
         * => new CIAProtector())); $this->pocBurner($poc, $outputHandler,
         * rand().rand()); $this->assertTrue($this->getOutput() !=
         * $cia->getRefreshPage());
         */
    }
}
