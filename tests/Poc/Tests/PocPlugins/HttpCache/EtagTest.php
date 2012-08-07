<?php

namespace Poc\Tests\PocPlugins\Output;

use Poc\Tests\PocTestCore;

use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\PocPlugins\HttpCache\Etag;

class EtagTest extends PocTestCore
{

    public function testEtagSend ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestMinify".  rand());

        $outputHandler = new TestOutput;

        $poc  = new Poc(array(PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                            PocParams::PARAM_HASHER=>$hasher
                        ));

        $poc->addPlugin(new PocLogs);

        $poc->addPlugin(new Etag);

        $testString = "whatever!123";

        $this->pocBurner($poc, $testString);

        echo serialize($outputHandler->getHeader());

        $header = $outputHandler->getHeader();

        $this->assertEquals(' ' . md5($testString), $header['Etag']);
    }
}
