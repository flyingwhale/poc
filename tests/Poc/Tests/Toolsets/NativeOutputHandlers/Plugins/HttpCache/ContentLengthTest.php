<?php

namespace Poc\Tests\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Tests\NativeOutputHandlersTestCore;

use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\ContentLength;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class ContentLenghtTest extends NativeOutputHandlersTestCore
{

    public function testContentLenght ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestContentLength".rand());

        $poc  = new Poc(array( PocParams::PARAM_HASHER=>$hasher ));
        $outputHandler = $poc->getPluginRegistry()->
        getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();
        
        $poc->addPlugin(new ContentLength);

        $testString = "123";

        $this->pocBurner($poc, $testString);

        $header = $outputHandler->getHeader();

        $this->assertEquals(strlen($testString), $header['Content-Length']);
    }
}
