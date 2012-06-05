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

use Poc\PocParams;
use Poc\Handlers\TestOutput;
use Poc\Poc;
use Poc\Cache\CacheImplementation\CacheParams;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\PocPlugins\Tagging\Doctrine2Tagging;

class Doctrine2TaggingPocTest extends \Poc\Tests\PocTestCore
{

    public function testTagging ()
    {
  
        $getPoc = function ($key, $addCacheAddTags='', 
                                                 $addCacheInvalidationTags='') {
            $getCache = function  () {
                return new FileCache(
                        array(CacheParams::PARAM_TTL =>  PocTestCore::BIGTTL ));
            };
            $hasher1 = new Hasher();
            $hasher1->addDistinguishVariable($key);
            $cache1 = $getCache($hasher1);
            $cache1->clearAll();

            $oh1 = new TestOutput();
            $poc1 = new Poc(array(PocParams::PARAM_CACHE => $cache1, 
                                  PocParams::PARAM_OUTPUTHANDLER => $oh1,
                                  PocParams::PARAM_HASHER => $hasher1));
            $tagger1 = new Doctrine2Tagging($GLOBALS['MYSQL_DBNAME'],
                                            'localhost',
                                            $GLOBALS['MYSQL_USER'],
                                            $GLOBALS['MYSQL_PASS']);
            $poc1->addPlugin($tagger1);
            if($addCacheAddTags) {
                $tagger1->addCacheAddTags(true, $addCacheAddTags);
            }
            if($addCacheInvalidationTags) {
                $tagger1->addCacheInvalidationTags(true, $addCacheInvalidationTags);
            }
            $ret['poc'] = $poc1; 
            $ret['tagger'] = $tagger1; 
            return $ret;
        };
        $poc1 = $getPoc('distn1','user,customer,inventory,a,b,c,d,e,f,b,h,i,j,k,l,m,n,o,p');
        $poc11 = $getPoc('distn2','user,customer,inventory');
        
        $poc2 = $getPoc('distn1','inventory','p');
        $poc3 = $getPoc('distn4','inventory,customer');
        
        $poc4 = $getPoc('distn4','inventory,customer','p');
        
        $this->pocBurner($poc1['poc'], "11");;
        $o1 = $this->getOutput();
        $poc1['tagger']->addCacheInvalidationTags(true, 'stuff');

        $this->pocBurner($poc11['poc'], "11");
        $o12 = $this->getOutput();

        $this->assertTrue($o1 == $o12);

        $poc2['tagger']->addCacheInvalidationTags(true, 'user');
        $this->pocBurner($poc2['poc'], "13");
        $o2 = $this->getOutput();

        $this->assertEquals("13", $o2);
        
        $poc3['tagger']->addCacheInvalidationTags(true, 'p');
        $this->pocBurner($poc3['poc'], "14");
        $o3 = $this->getOutput();
        
        $this->pocBurner($poc3['poc'], "15");
        $o4 = $this->getOutput();
        
        $this->assertEquals("14", $o4);
        
    }
}
