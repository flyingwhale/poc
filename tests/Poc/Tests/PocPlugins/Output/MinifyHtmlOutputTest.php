<?php

namespace Poc\Tests\PocPlugins\Output;

use Poc\Tests\Toolsets\NativeOutputHandlers\NativeOutputHandlersTestCore;

use Poc\PocPlugins\Output\MinifyHtmlOutput;
use Poc\PocParams;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class MinifyHtmlOutputTest extends NativeOutputHandlersTestCore
{
    const TEST_STRING_MINIFY_EXTRA_SPACE = "A    a      A";

    public function dataProviderForTests ()
    {
        return array(
                    array("A    a      A", "A a A"),
                    array("A
                        a      A", "A a A"),
                    array("A
a
                A", "A a A"),
                    array("A
        a
                A", "A a A"),

                array("A    a  <!-- html comment -->    A", "A a A"),

                );

    }

    /**
     * @dataProvider dataProviderForTests
     */
    public function testminifyHtmlWithPoc ($input, $expectedOutput)
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("TestMinify".  rand());

        $cache = new FileCache(
                                array(CacheParams::PARAM_TTL => self::BIGTTL,
                                    ));

        $outputHandler = new TestOutput();

        $poc  = new Poc(array(PocParams::PARAM_CACHE => new FileCache(),
                              PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                              PocParams::PARAM_CACHE=>$cache,
                              PocParams::PARAM_HASHER=>$hasher,
                              Poc::PARAM_TOOLSET => new HttpCapture(new TestOutput())
                        ));

        $poc->addPlugin(new MinifyHtmlOutput);

        $this->pocBurner($poc, $input);
        $output = $this->getOutput();

        $this->assertEquals($expectedOutput, $output);
    }
}
