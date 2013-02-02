<?php

namespace Poc\Tests\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Tests\PocTestCore;

use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\ContentLength;

class ContentLenghtTest extends PocTestCore
{

    public function testContentLenght ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestMinify".  rand());

        $outputHandler = new TestOutput;

        $poc  = new Poc(array(PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                            PocParams::PARAM_HASHER=>$hasher
                        ));

        $poc->addPlugin(new ContentLength);

        $testString = "123";

        $this->pocBurner($poc, $testString);

        var_dump($outputHandler->getHeader());

        $header = $outputHandler->getHeader();

        $this->assertEquals(strlen($testString), $header['Content-Length']);
    }
}
