<?php

namespace Poc\Tests\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Tests\PocTestCore;

use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\Etag;

class EtagTest extends PocTestCore
{

    const ETAG_MD5 = 'c075eba9c04d3faf4ac21fd29cae6fd8';
    const ETAG_TEXT = 'whatever!123';

    public function testEtagSend ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestEtag1" . rand());

        $outputHandler = new TestOutput;

        $poc  = new Poc(array(PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                              PocParams::PARAM_HASHER=>$hasher
                        ));

        $poc->addPlugin(new Etag);

        $this->pocBurner($poc, self::ETAG_TEXT);

        $header = $outputHandler->getHeader();

        $this->assertEquals(md5(self::ETAG_TEXT), $header['Etag']);
    }

    public function testEtagReceive ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestEtag2" . rand());

        $outputHandler = new TestOutput;

        $outputHandler->allheaders['If-None-Match'] = 'c075eba9c04d3faf4ac21fd29cae6fd8';
        echo"TT";
        echo serialize($outputHandler->getallheaders());
        echo"TT";
        $poc  = new Poc(array(PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                              PocParams::PARAM_HASHER=>$hasher
                        ));

        $poc->addPlugin(new Etag);

        $this->pocBurner($poc, self::ETAG_TEXT);

        $header = $outputHandler->getHeader();

        $this->assertTrue(isset($header['HTTP/1.0 304 Not Modified']));
    }
}
