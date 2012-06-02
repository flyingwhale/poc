<?php
/*
 * Copyright 2011 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\Tests\PocPlugins\Tagging;

use Poc\Tests\PocTestCore;

use Poc\Cache\Filtering\OutputFilter;
use Poc\PocParams;
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
use Poc\PocPlugins\Tagging\Doctrine2Tagging;

class Doctrine2TaggingPocTest extends \Poc\Tests\PocTestCore
{
    
    public function testTagging ()
    {
        $getCache = function  ($hasher) {
            return new FileCache(
                    array(CacheParams::PARAM_TTL =>  PocTestCore::BIGTTL, 
                                         CacheParams::PARAM_HASHER => $hasher));
        };

        $hasher1 = new Hasher();
        $hasher1->addDistinguishVariable("distn1");
        $cache1 = $getCache($hasher1);
        $cache1->clearAll();
        
        $oh1 = new TestOutput();
        $poc1 = new Poc(array(PocParams::PARAM_CACHE => $cache1, PocParams::PARAM_OUTPUTHANDLER => $oh1));
        $tagger1 = new Doctrine2Tagging($GLOBALS['MYSQL_DBNAME'], 
                                        'localhost', 
                                        $GLOBALS['MYSQL_USER'], 
                                        $GLOBALS['MYSQL_PASS']);
        $poc1->addPlugin($tagger1);
        $tagger1->addCacheAddTags(true, "user,customer,inventory");
        
        //----------------------------------------------------------------------
        $hasher2 = new Hasher();
        $hasher2->addDistinguishVariable("distn2");
        $cache2 = $getCache($hasher1);
        $oh2 = new TestOutput();
        $poc2 = new Poc(array(PocParams::PARAM_CACHE => $cache2, PocParams::PARAM_OUTPUTHANDLER => $oh2));

        $tagger2 = new Doctrine2Tagging($GLOBALS['MYSQL_DBNAME'], 
                                        'localhost', 
                                        $GLOBALS['MYSQL_USER'], 
                                        $GLOBALS['MYSQL_PASS']);
        $poc2->addPlugin($tagger2);
        $tagger2->addCacheAddTags(true, "inventory");

        //----------------------------------------------------------------------
        $hasher3 = new Hasher();
        $hasher3->addDistinguishVariable("distn3");
        $cache3 = $getCache($hasher3);
        $oh3 = new TestOutput();
        $poc3 = new Poc(array(PocParams::PARAM_CACHE => $cache3, PocParams::PARAM_OUTPUTHANDLER => $oh3,
                              ));
        $tagger3 = new Doctrine2Tagging($GLOBALS['MYSQL_DBNAME'], 
                                        'localhost', 
                                        $GLOBALS['MYSQL_USER'], 
                                        $GLOBALS['MYSQL_PASS']);
        $poc3->addPlugin($tagger3);
        $tagger3->addCacheAddTags(true, "inventory");
        $tagger3->addCacheAddTags(true, "customer");

        //----------------------------------------------------------------------
        $this->pocBurner($poc1, $oh1, "11");;
        $o1 = $this->getOutput();
        $tagger1->addCacheInvalidationTags(true, 'stuff');
//        die("stuff ----------------------");

        $this->pocBurner($poc1, $oh1, "11");
        $o12 = $this->getOutput();
        

        $this->assertTrue($o1 == $o12);

        $tagger2->addCacheInvalidationTags(true, 'user');

        $this->pocBurner($poc2, $oh2, "13");
        $o13 = $this->getOutput();
        
//        $this->assertEquals("13", $o13);
        $this->assertEquals("13", $o13);
        //more relevan tests shuld be implemented.
/*        
        $tagger1->addCacheInvalidationTags(true, 'customer');

        $this->pocBurner($poc1, $oh1, "4");
        $o1 = $this->getOutput();
        $this->pocBurner($poc2, $oh2, "4");
        $o2 = $this->getOutput();
        $this->pocBurner($poc3, $oh3, "4");
        $o3 = $this->getOutput();

        $this->assertTrue($o1 == $o3);
        $this->assertTrue($o2 != $o3);
*/
    }
}
