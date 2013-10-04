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

namespace Poc\Tests\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Tests\Toolsets\NativeOutputHandlers\NativeOutputHandlersTestCore;

use Poc\PocParams;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\ContentLength;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;

class ContentLenghtTest extends NativeOutputHandlersTestCore
{

    public function testContentLenght ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestContentLength".rand());

        $poc  = new Poc(array( Poc::PARAM_TOOLSET =>
                                              new HttpCapture(new TestOutput()),
                               PocParams::PARAM_HASHER=>$hasher ));
        $outputHandler = $poc->getEventDispatcher()->
        getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();

        $poc->addPlugin(new ContentLength);

        $testString = "123";

        $this->pocBurner($poc, $testString);

        $header = $outputHandler->getHeader();

        $this->assertEquals(strlen($testString), $header['Content-Length']);
    }
}
