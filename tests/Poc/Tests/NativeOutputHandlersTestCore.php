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
use Poc\Poc;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

const UNITTESTING = 1;

class NativeOutputHandlersTestCore 
//extends \PHPUnit_Framework_TestCase
{

    protected $analizeThisOutput;
    protected $analizeThisHeader;

    protected function setOutput ($o)
    {
        $this->analizeThisOutput = $o;
    }

    public function getOutput ()
    {
        return $this->analizeThisOutput;
    }

    public function getHeader ()
    {
        return $this->analizeThisHeader;
    }

    protected function setHeader ($header)
    {
        $this->analizeThisHeader = $header;
    }

    /**
     *
     * @param $cache Cache
     * @param $testString string
     */
    public function cacheBurner ($cache, $testString = "testString")
    {
        $poc = new Poc(array(PocParams::PARAM_CACHE => $cache));
        $this->pocBurner($poc, $testString);
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
    public function pocBurner (Poc $poc, $testString = "testString")
    {
        $poc->addPlugin(new PocLogs());

        $this->setOutput('');
        $poc->start();

        $outputHandler = $poc->getPluginRegistry()->getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();
        
        if ($outputHandler->getOutputFlow()) {
            echo $testString;
            $poc->end();
            
            //var_dump($outputHandler);
            
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
        } 
        else 
        {
            $this->setHeader($outputHandler->getHeader());
            $this->setOutput($outputHandler->getOutput());
            $poc->end();
            
            //var_dump($outputHandler);

            if ($outputHandler->getOutput()) {
                $this->setHeader($outputHandler->getHeader());
                $this->setOutput($outputHandler->getOutput());
            }
        }
    }
}
