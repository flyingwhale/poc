<?php

namespace Poc\Tests\PocPlugins\Output;

use Poc\Tests\PocTestCore;

use Poc\PocParams;
use Poc\Handlers\Output\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\PocPlugins\Output\OutputFilter;

class OutputFilterTest extends PocTestCore
{

    public function testOutputFilter ()
    {
        $hasher = new Hasher();
        $hasher->addDistinguishVariable("testOutputFilter".  rand());

        $outputHandler = new TestOutput();
        $cache = new FileCache(
                array(CacheParams::PARAM_TTL => self::BIGTTL,
                      ));
        $outputFilter = new OutputFilter();

        $outputFilter->addBlacklistCondition(self::NEEDLE);
        $poc = new Poc(
                array(PocParams::PARAM_CACHE => $cache,
                      PocParams::PARAM_OUTPUTHANDLER => $outputHandler,
                      PocParams::PARAM_OUTPUTFILTER => $outputFilter,
                      PocParams::PARAM_DEBUG => true,
                      PocParams::PARAM_HASHER => $hasher,
                      ));

        $poc->addPlugin($outputFilter);

        $this->pocBurner($poc, rand() . self::NEEDLE . rand());
        $this->assertContains('because', $this->getOutput());
    }

}
