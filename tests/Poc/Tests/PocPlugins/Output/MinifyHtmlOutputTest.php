<?php

namespace Poc\Tests\PocPlugins\Output;

use Poc\Tests\PocTestCore;

use Poc\PocPlugins\Output\MinifyHtmlOutput;
use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;

class MinifyHtmlOutputTest extends PocTestCore
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
                            PocParams::PARAM_HASHER=>$hasher
                        ));

        $poc->addPlugin(new PocLogs);

        $poc->addPlugin(new MinifyHtmlOutput);

        $this->pocBurner($poc, $input);
        $output = $this->getOutput();

        $this->assertEquals($expectedOutput, $output);
    }
}
