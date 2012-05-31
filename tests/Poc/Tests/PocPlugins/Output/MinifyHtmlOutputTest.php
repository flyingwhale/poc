<?php

namespace unittest;

require_once __DIR__.'/../../PocTestCore.php';

use Poc\PocPlugins\Output\MinifyHtmlOutput;
use Poc\PocPlugins\Logging\PocLogsParams;
use Poc\PocPlugins\Logging\PocLogs;
use Poc\PocParams;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;

class MinifyHtlmOutputTest extends PocTestCore
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
                                    CacheParams::PARAM_HASHER => $hasher));

        $outputHandler = new TestOutput();

        $poc  = new Poc(array(PocParams::PARAM_CACHE => new FileCache(),
                            PocParams::PARAM_OUTPUTHANDLER=> $outputHandler,
                            PocParams::PARAM_CACHE=>$cache,
                        ));

        $poc->addPlugin(new PocLogs);
        
        $poc->addPlugin(new MinifyHtmlOutput);

        $this->pocBurner($poc, $outputHandler, $input);
        $output = $this->getOutput();

        $this->assertEquals($expectedOutput, $output);
    }
}
