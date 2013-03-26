<?php

namespace Poc\Tests\Toolsets\NativeOutputHandlers\Plugins\HttpCache;

use Poc\Tests\Toolsets\NativeOutputHandlers\NativeOutputHandlersTestCore;

use Poc\PocParams;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Plugins\HttpCache\Etag;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class EtagTest extends NativeOutputHandlersTestCore
{

    const ETAG_MD5 = 'c075eba9c04d3faf4ac21fd29cae6fd8';
    const ETAG_TEXT = 'whatever!123';

    public function testEtagSend ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestEtag1" . rand());

        $poc  = new Poc(array(PocParams::PARAM_HASHER=>$hasher,
                              Poc::PARAM_TOOLSET =>
                                              new HttpCapture(new TestOutput()),
                ));

        $outputHandler = $poc->getPluginRegistry()->
                        getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();

        $poc->addPlugin(new Etag);

        $this->pocBurner($poc, self::ETAG_TEXT);

        $header = $outputHandler->getHeader();

        $this->assertEquals(md5(self::ETAG_TEXT), $header['Etag']);
    }

    public function testEtagReceive ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestEtag2" . rand());

        $poc  = new Poc(array(Poc::PARAM_TOOLSET =>
                                              new HttpCapture(new TestOutput()),
                              PocParams::PARAM_HASHER=>$hasher));

        $outputHandler = $poc->getPluginRegistry()->
                        getPlugin(HttpCapture::PLUGIN_NAME)->getOutputHandler();

        $outputHandler->allheaders['If-None-Match'] =
                                             'c075eba9c04d3faf4ac21fd29cae6fd8';
        $poc->addPlugin(new Etag);
        $this->pocBurner($poc, self::ETAG_TEXT);

        $header = $outputHandler->getHeader();

        $this->assertTrue(isset($header['HTTP/1.0 304 Not Modified']));
    }
}
