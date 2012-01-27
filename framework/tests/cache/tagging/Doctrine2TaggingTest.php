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
use POC\cache\tagging\Doctrine2Tagging;

require_once 'framework/autoload.php';

class Doctrine2TaggingTest extends \PHPUnit_Extensions_Database_TestCase
{
  /*
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
  */
  public function getConnection()
  {
    $dsn = 'mysql:dbname='.Doctrine2Tagging::DEFDB.';host='.Doctrine2Tagging::DEFHOST;
    $pdo = new \PDO($dsn, Doctrine2Tagging::DEFUSER, Doctrine2Tagging::DEFPASS);

    return $this->createDefaultDBConnection($pdo);
  }

  public function getDataSet()
  {
    return $this->createXMLDataSet('framework/tests/cache/tagging/fixture/tagging/init.xml');
  }

  /**
  * @dataProvider addCacheToTagsProvider
  */
  public function testAddCacheToTags($tagsString, $hash, $expires, $expectedDatasetPath)
  {
    $tagging = new Doctrine2Tagging();

    $tagging->addCacheToTags($tagsString, $hash, $expires);

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));

    $exepctedDataSet = $this->createXMLDataSet($expectedDatasetPath.'-01.xml');
    $this->assertDataSetsEqual($exepctedDataSet, $dataSet);

    $tagging->addCacheToTags($tagsString, $hash, $expires);

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));
    $exepctedDataSet = $this->createXMLDataSet($expectedDatasetPath.'-02.xml');

    $this->assertDataSetsEqual($exepctedDataSet, $dataSet, 'addCacheToTags() duplication test');
  }

  /**
  * @dataProvider flushOutdatedProvider
  */
  public function testFlushOutdated($initDatasetPath, $expectedDatasetPath)
  {
    $initDataSet = $this->createXMLDataSet($initDatasetPath);
    $this->getDatabaseTester()->setDataSet($initDataSet);
    $this->getDatabaseTester()->onSetUp();

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));
    $expectedDataset = $initDataSet;

    
    $this->assertDataSetsEqual($expectedDataset, $dataSet);

    $tagging = new Doctrine2Tagging();
    $tagging->flushOutdated();

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));
    $expectedDataset = $this->createXMLDataSet($expectedDatasetPath);
    $this->assertDataSetsEqual($expectedDataset, $dataSet);
  }
  /**
  * @dataProvider tagInvalidateProvider
  */
  public function testTagInvalidate($initDatasetPath, $invalidateTag, $expectedDatasetPath)
  {
    /*
     * This function use mock object to emulate cacheSpecificClearItem
     * 
     * A short example
     * 
     * A cache -> tag1, tag2
     * B cache -> tag2, tag3
     * C cache-> tag2
     * tag2 invalidate -> clear all (A, B, C caches) 
     */
    $initDataSet = $this->createXMLDataSet($initDatasetPath);
    $this->getDatabaseTester()->setDataSet($initDataSet);
    $this->getDatabaseTester()->onSetUp();

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));
    $expectedDataset = $initDataSet;
    $this->assertDataSetsEqual($expectedDataset, $dataSet);

    $cacheMock = $this->getMock('Cache', array('cacheSpecificClearItem'));
    $cacheMock->expects($this->any())
    ->method('cacheSpecificClearItem')
    ->will($this->returnValue(true));

    $tagging = new Doctrine2Tagging();
    
    $tagging->tagInvalidate($invalidateTag);

    $dataSet = $this->getConnection()->createDataSet(array('caches', 'tags_has_caches', 'tags'));
    $expectedDataset = $this->createXMLDataSet($expectedDatasetPath);
    $this->assertDataSetsEqual($expectedDataset, $dataSet);
  }
  
  public static function addCacheToTagsProvider()
  {
    $basePath = 'framework/tests/cache/tagging/fixture/tagging/addCacheToTags/';
    $data = array(
      array(
        'tag1,tag2',
        '12345678901234567890123456789012',
        1234,    
        $basePath.'expected_01'
      )
    );
  
    return $data;
  }
  
  public static function flushOutdatedProvider()
  {
    $basePath = 'framework/tests/cache/tagging/fixture/tagging/flushOutdated/';
    $data = array(
      array($basePath.'init_01.xml',
        $basePath.'expected_01.xml'
      )
    );
  
    return $data;
  }

  public static function tagInvalidateProvider()
  {
    $basePath = 'framework/tests/cache/tagging/fixture/tagging/tagInvalidate/';
    $data = array(
    array(
      $basePath.'init_01.xml',
      'tag2',
      $basePath.'expected_01.xml'
    )
    );
  
    return $data;
  }
}

