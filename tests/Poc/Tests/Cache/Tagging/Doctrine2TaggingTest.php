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

namespace Poc\Tests\Cache\Tagging;

use Poc\Cache\Tagging\Doctrine2Tagging;

class Doctrine2TaggingTestold extends \PHPUnit_Extensions_Database_TestCase
{

    protected $fixtureDirPath;

    public function setUp ()
    {
        $this->fixtureDirPath = __DIR__ . '/fixture/tagging';
        parent::setUp();
    }

    /*
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection ()
    {
        $pdo = new \PDO($GLOBALS['MYSQL_DSN'], $GLOBALS['MYSQL_USER'], $GLOBALS['MYSQL_PASS']);
        $query = 'USE ' . $GLOBALS['MYSQL_DBNAME'];
        $pdo->exec($query);

        return $this->createDefaultDBConnection($pdo);
    }

    public function getDataSet ()
    {
        return $this->createXMLDataSet($this->fixtureDirPath . '/init.xml');
    }

    public function getDoctrine2Tagging()
    {
        $tagging = new Doctrine2Tagging($GLOBALS['MYSQL_DBNAME'], 'localhost', $GLOBALS['MYSQL_USER'], $GLOBALS['MYSQL_PASS']);

        return $tagging;
    }

    /**
     * @dataProvider addCacheToTagsProvider
     */
    public function testAddCacheToTags ($tagsString, $hash, $expires,
            $expectedDatasetFilename)
    {
        $expectedDatasetPath = $this->fixtureDirPath . '/addCacheToTags/' . $expectedDatasetFilename;

        $tagging = $this->getDoctrine2Tagging();

        $tagging->addCacheToTags($tagsString, $hash, $expires);

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));

        $exepctedDataSet = $this->createXMLDataSet(
                $expectedDatasetPath . '-01.xml');
        $this->assertDataSetsEqual($exepctedDataSet, $dataSet);

        $tagging->addCacheToTags($tagsString, $hash, $expires);

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));
        $exepctedDataSet = $this->createXMLDataSet(
                $expectedDatasetPath . '-02.xml');

        $this->assertDataSetsEqual($exepctedDataSet, $dataSet,
                'addCacheToTags() duplication test');
    }

    /**
     * @dataProvider flushOutdatedProvider
     */
    public function testFlushOutdated ($initDatasetFilename,
            $expectedDatasetFilename)
    {
        $initDatasetPath = $this->fixtureDirPath . '/flushOutdated/' . $initDatasetFilename;
        $expectedDatasetPath = $this->fixtureDirPath . '/flushOutdated/' . $expectedDatasetFilename;

        $initDataSet = $this->createXMLDataSet($initDatasetPath);
        $this->getDatabaseTester()->setDataSet($initDataSet);
        $this->getDatabaseTester()->onSetUp();

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));
        $expectedDataset = $initDataSet;

        $this->assertDataSetsEqual($expectedDataset, $dataSet);

        $tagging = $this->getDoctrine2Tagging();
        $tagging->flushOutdated();

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));
        $expectedDataset = $this->createXMLDataSet($expectedDatasetPath);
        $this->assertDataSetsEqual($expectedDataset, $dataSet);
    }

    /**
     * @dataProvider tagInvalidateProvider
     */
    public function testTagInvalidate ($initDatasetFilename, $invalidateTag,
            $expectedDatasetFilename)
    {
        /*
         * This function use mock object to emulate cacheSpecificClearItem A
         * short example A cache -> tag1, tag2 B cache -> tag2, tag3 C cache->
         * tag2 tag2 invalidate -> clear all (A, B, C caches)
         */

        $initDatasetPath = $this->fixtureDirPath . '/tagInvalidate/' . $initDatasetFilename;
        $expectedDatasetPath = $this->fixtureDirPath . '/tagInvalidate/' . $expectedDatasetFilename;

        $initDataSet = $this->createXMLDataSet($initDatasetPath);
        $this->getDatabaseTester()->setDataSet($initDataSet);
        $this->getDatabaseTester()->onSetUp();

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));
        $expectedDataset = $initDataSet;
        $this->assertDataSetsEqual($expectedDataset, $dataSet);

        $cacheMock = $this->getMock('Cache', array('cacheSpecificClearItem'));
        $cacheMock->expects($this->any())
            ->method('cacheSpecificClearItem')
            ->will($this->returnValue(true));

        $tagging = $this->getDoctrine2Tagging();

        $tagging->tagInvalidate($invalidateTag);

        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));
        $expectedDataset = $this->createXMLDataSet($expectedDatasetPath);
        $this->assertDataSetsEqual($expectedDataset, $dataSet);
    }

    public static function addCacheToTagsProvider ()
    {
        $data = array(array('tag1,tag2', '12345678901234567890123456789012', 1234, 'expected_01'));

        return $data;
    }

    public static function flushOutdatedProvider ()
    {
        $data = array(array('init_01.xml', 'expected_01.xml'));

        return $data;
    }

    public static function tagInvalidateProvider ()
    {
        $data = array(array('init_01.xml', 'tag2', 'expected_01.xml'));

        return $data;
    }
}

