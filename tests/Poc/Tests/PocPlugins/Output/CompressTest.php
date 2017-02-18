<?php
/*
 * Copyright 2013 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\Tests\PocPlugins\Output;


use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Poc;
use Poc\PocParams;
use Poc\PocPlugins\Output\Compress;
use Poc\Tests\Toolsets\NativeOutputHandlers\NativeOutputHandlersTestCore;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Output\TestOutput;
use Poc\Toolsets\NativeOutputHandlers\HttpCapture;

class CompressTest extends \PHPUnit_Framework_TestCase
{
    const TEST_STRING_MINIFY_EXTRA_SPACE = "A    a      A";

    public function testCompression ()
    {
       if (function_exists("gzcompress")) {
         $noh = new NativeOutputHandlersTestCore();
         $hasher = new Hasher();
         $hasher->addDistinguishVariable("TestCompress".  rand());
         $cache = new FileCache();
         $outputHandler = new TestOutput();
         $outputHandler->header('Accept-Encoding:'.Compress::COMPRESSION_GZIP);

         $poc = new Poc(array(Poc::PARAM_CACHE => $cache,
                              Poc::PARAM_TOOLSET => new HttpCapture($outputHandler),
                              PocParams::PARAM_HASHER => $hasher));

         $poc->addPlugin(new Compress());

         $input1 = "absdefg";
         $noh->pocBurner($poc, $input1);
         $output1 = $noh->getOutput();
         $header1 = $outputHandler->getallheaders();

         $input2 = "habsdefgh";
         $noh->pocBurner($poc, $input2);
         $output2 = $noh->getOutput();
         $header2 = $outputHandler->getallheaders();

         $this->assertEquals($output1, $output2);
         $this->assertEquals(\gzdecode($output1), $input1);
       } 
    }
}
