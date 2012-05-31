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

namespace Poc\Tests;

use Poc\PocPlugins\Logging\PocLogs;

use Poc\PocParams;
use Poc\Handlers\TestOutput;
use Poc\Poc;

const UNITTESTING = 1;

abstract class PocTestCore extends \PHPUnit_Framework_TestCase
{
    const TESTSTRING1 = "1";
    const TESTSTRING2 = "2";
    const TESTSTRING3 = "3";
    const BIGTTL = 100;
    const NEEDLE = '/amiga1200/';

    public static $TTL = 5;

    protected $analizeThisOutput;
    protected $analizeThisHeader;

    public static function setUpBeforeClass()
    {
        PocTestCore::$TTL = $GLOBALS['TTL'];
        \ob_start(function($output){return"";});
    }

    protected function setOutput ($o)
    {
        $this->analizeThisOutput = $o;
    }

    protected function getOutput ()
    {
        return $this->analizeThisOutput;
    }

    protected function getHeader ()
    {
        return $this->analizeThisHeader;
    }

    protected function setHeader ($header)
    {
        $this->analizeThisHeader = $header;
    }
    /*
     * public function __construct(){ new PocLogs(); }
     */

    /**
     *
     * @param $cache Cache
     * @param $testString string
     */
    protected function cacheBurner ($cache, $testString = "testString")
    {
        $outputHandler = new TestOutput();
        $poc = new Poc(
                array(PocParams::PARAM_CACHE => $cache, PocParams::PARAM_OUTPUTHANDLER => $outputHandler));
        $this->pocBurner($poc, $outputHandler, $testString);
    }

    /**
     * This function has got a weird name, because it does not do anything else
     * only inspect the getOutputFlow function of the output handler and decides
     * what to do with the $testsring variable it receives. This tries to
     * emulate the behahviour of the server to the $poc object.
     *
     * @param $poc Poc
     * @param $outputHandler TestOutput
     * @param $testString string
     */
    protected function pocBurner (Poc $poc, $outputHandler,
                                                     $testString = "testString")
    {
        $poc->addPlugin(new PocLogs());

        $this->setOutput('');
        $poc->start();

        if ($outputHandler->getOutputFlow()) {
            echo $testString;
            $poc->destruct();
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
        } else {
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
            $poc->destruct();

            if ($outputHandler->getOutput()) {
                $this->setHeader($outputHandler->getHeader());
                $this->setOutput($outputHandler->getOutput());
            }
        }
    }

}

