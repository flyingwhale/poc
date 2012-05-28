<?php

namespace unittest;

require_once __DIR__.'/../PocTestCore.php';

use Poc\PocPlugins\MinifyHtmlOutput;
use Poc\PocPlugins\PocLogsParams;
use Poc\PocPlugins\PocLogs;
use Poc\Cache\CacheInvalidationProtection\CIAProtector;
use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\CacheImplementation\MemcachedCache;
use Poc\Cache\CacheImplementation\RediskaCache;
use Poc\Cache\CacheImplementation\MongoDBCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Poc\Cache\Tagging\MysqlTagging;

class MinifyHtlmOutputTest extends PocTestCore
{
    const TEST_STRING_MINIFY_EXTRA_SPACE = "A    
        

      A";
    
    public function testOutputFilter ()
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
        
        new PocLogs(array(PocLogsParams::PARAM_POC => $poc));
        new MinifyHtmlOutput($poc->getPocDispatcher());
        
        $this->pocBurner($poc, $outputHandler, self::TEST_STRING_MINIFY_EXTRA_SPACE);
        $output = $this->getOutput();
                
        $this->assertNotEquals(self::TEST_STRING_MINIFY_EXTRA_SPACE, $output);
        $this->assertEquals("A A", $output);
        
    }

}