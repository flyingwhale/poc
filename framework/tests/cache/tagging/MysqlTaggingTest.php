<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

namespace unittest;

//include_once '../../../autoload.php';
use POC\cache\tagging\MysqlTagging;
use POC\cache\tagging\driver\mysql\CacheModelManager;
use POC\cache\tagging\driver\mysql\TagModelManager;
use POC\cache\tagging\driver\mysql\TagsHasCachesModelManager;
use POC\cache\tagging\driver\mysql\model\Cache;
use POC\cache\tagging\driver\mysql\model\Tag;
use POC\cache\tagging\driver\mysql\model\TagCache;

use POC\cache\filtering\Hasher;
use POC\cache\filtering\filter;

class MysqlTaggingTest extends \PHPUnit_Framework_TestCase
{
  protected static $PDO;
  protected static $dsn;

  protected static $cmm;
  protected static $tmm;
  protected static $tcmm;

  protected $tagging;
  
  public static function setUpBeforeClass()
  {
    self::$dsn = 'mysql:dbname='.MysqlTagging::DEFDB.';host='.MysqlTagging::DEFHOST;
    self::$PDO = new \PDO(self::$dsn, MysqlTagging::DEFUSER, MysqlTagging::DEFPASS);

    self::$cmm = new CacheModelManager(self::$PDO);
    self::$tmm = new TagModelManager(self::$PDO);
    self::$tcmm = new TagsHasCachesModelManager(self::$PDO);
  }

  public static function tearDownAfterClass()
  {
    self::$PDO = null;
  }
  function setUp()
  {
    $this->tagging = new MysqlTagging();
    $this->tagging->truncateTables();
     
  }

  function testAddCacheToTags()
  {
    $tagsString = 'tag1,tag2';
    $hash = '12345678901234567890123456789012';
    $expires = 1234;
    
    $this->tagging->addCacheToTags($tagsString, $hash, $expires);

    $testCache = new Cache();
    $testCache->id = 1;
    $testCache->hash = $hash;
    $testCache->expires = $expires;

    $cache = self::$cmm->findOneBy('hash', $hash);
    $this->assertEquals($cache, $testCache);

    $testTag = new Tag();
    $testTag->id = 1;
    $testTag->tag = 'tag1';

    $tag = self::$tmm->find(1);

    $this->assertEquals($tag, $testTag);

    $testTag = new Tag();
    $testTag->id = 2;
    $testTag->tag = 'tag2';

    $tag = self::$tmm->find(2);

    $this->assertEquals($tag, $testTag);

    $testTagCache = new TagCache();
    $testTagCache->tag_id = 1;
    $testTagCache->cache_id = 1;

    $tagCache = self::$tcmm->find(1, 1);

    $this->assertEquals($tagCache, $testTagCache);

    $testTagCache = new TagCache();
    $testTagCache->tag_id = 2;
    $testTagCache->cache_id = 1;

    $tagCache = self::$tcmm->find(2, 1);

    $this->assertEquals($tagCache, $testTagCache);

    // duplication test start 
    $this->tagging->addCacheToTags($tagsString, $hash, $expires);

    
    $cache = self::$cmm->findAll();
    $this->assertCount(1, $cache);

    $tagsCaches = self::$tcmm->findAll();
    $this->assertCount(2, $tagsCaches);
    
    $tags = self::$tmm->findAll();
    $this->assertCount(2, $tags);
    
  }


  function testTagInvalidate()
  {
    // it use file cache
    // A cache -> tag1, tag2
    // B cache -> tag2, tag3
    // C cache-> tag2
    // tag2 invalidate -> clear all
    
    $cacheMock = $this->getMock('FileCache', array('cacheSpecificClearItem'));
    $cacheMock->expects($this->any())
    ->method('cacheSpecificClearItem')
    ->will($this->returnValue(true));
        
    $this->tagging->addCache($cacheMock);
     
    $hash = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
    $tagsString = 'tag1,tag2';
    $expires = 1234;

    $this->tagging->addCacheToTags($tagsString, $hash, $expires);
    
    $hash = 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB';
    $tagsString = 'tag2,tag3';
    $expires = 1234;

    $this->tagging->addCacheToTags($tagsString, $hash, $expires);
    
    $hash = 'CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC';
    $tagsString = 'tag2';
    $expires = 1234;
    
    $this->tagging->addCacheToTags($tagsString, $hash, $expires);
    $invalidateTag = 'tag2';
    $this->tagging->tagInvalidate($invalidateTag);
    
    $cache = self::$cmm->findAll();
    $this->assertCount(0, $cache);
    
    $tagsCaches = self::$tcmm->findAll();
    $this->assertCount(0, $tagsCaches);
  }
  
  function testFlushOutdated()
  {
    $tagsString = 'tag1,tag2';
    $hash = '12345678901234567890123456789012';
    $expires = 1234;
    
    $this->tagging->addCacheToTags($tagsString, $hash, $expires);
    $this->tagging->flushOutdated();

    $cache = self::$cmm->findAll();
    $this->assertCount(0, $cache);
    
    $tagsCaches = self::$tcmm->findAll();
    $this->assertCount(0, $tagsCaches);
    
    $tags = self::$tmm->findAll();
    $this->assertCount(0, $tags);
    
  }
}

