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

namespace Poc\Toolsets\NativeOutputHandlers\Handlers\Output;

use Poc\Poc;
use Optionable;

class TestOutput extends Output
{

    const HEADER_BASIC = 'a:1:{i:0;s:35:"X-Powered-By: PHP/5.3.6-13ubuntu3.3";}';

    const PARAM_HEADER = 'header';

    /**
     * Stores the serialized arrays of "fake" headers for testing reasons.
     *
     * @var string
     */
    private $actHeader;

    private $header = null;

    private $outputFlow = 1;

    private $output = '';

    private $optionable;

    public $allheaders;

    public function setupDefaults ()
    {
        $this->optionable->setDefaultOption(self::PARAM_HEADER, self::HEADER_BASIC);
    }

    public function __construct ($options = array())
    {
        $this->optionable = new Optionable($options, $this);
        $this->setupDefaults();
        $this->actHeader = $this->optionable[self::PARAM_HEADER];
    }

    public function getLevel ()
    {
        return ob_get_level();
    }

    public function startBuffer ($callbackFunctname)
    {
        ob_start(array($this->callbackHandler, $callbackFunctname),0);
    }

    public function stopBuffer ($output = '')
    {
        if ($this->outputFlow) {
            echo ($output);
            $this->outputFlow = 0;
//          in real life this is a realoutput callback
            $this->callbackHandler->pocCallbackCache($output);
//          This part works perfectly under php5.4, but php5.3 wiht phpunit just segfaults
//          ob_end_flush();
        }
    }

    public function header ($header)
    {
        if (strstr($header,':')) {
            $headerArray = explode(":", $header);
            $this->header[$headerArray[0]] = trim ($headerArray[1]);
        }
    }

    public function getHeader ()
    {
        return $this->header;
    }

    public function obEnd ()
    {
        if ($this->outputFlow) {
            $this->outputFlow = 0;
            ob_flush();
        }
    }

    public function getOutputFlow ()
    {
        return $this->outputFlow;
    }

    public function ObPrintCallback ($output)
    {
        $this->output = $output;
    }

    public function getOutput ()
    {
        return $this->output;
    }

    /**
     *
     * @return array The actual fake header list;
     */
    public function headersList ()
    {
        return \unserialize($this->actHeader);
    }

    public function getallheaders()
    {
        return $this->allheaders;
    }

}
