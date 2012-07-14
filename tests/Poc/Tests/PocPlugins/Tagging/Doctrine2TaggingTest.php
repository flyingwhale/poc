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

use Poc\PocPlugins\Tagging\Doctrine2Tagging;
use Poc\Optionable\DoctrineOptionable;

abstract class Doctrine2TaggingTest extends \PHPUnit_Extensions_Database_TestCase
{

    protected $fixtureDirPath;

    protected static $pdo;

    protected static $doctrineOptions;

    public static function setUpBeforeClass()
    {
        self::$doctrineOptions = static::getDoctrineOptionableOptions();

        $doctrineOptionAble = new DoctrineOptionable(self::$doctrineOptions);
        $em = $doctrineOptionAble['orm.entity_managers.default'];

        $conn = $em->getConnection();

        static::cleanDatabase($em);
        self::$pdo = $conn->getWrappedConnection();
    }

    public static function getDoctrineOptionableOptions()
    {
        $options = $GLOBALS['DOCTRINE_OPTIONABLE'];

        return $options;
    }

    public static function cleanDatabase($em)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array(
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Cache'),
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\CacheTag'),
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Tag')

        );
        $tool->dropDatabase();
        $tool->createSchema($classes);
    }

    public static function tearDownAfterClass()
    {
        self::$pdo = null;
    }

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

        return $this->createDefaultDBConnection(self::$pdo);
    }

    public function getDataSet ()
    {
        return $this->createXMLDataSet($this->fixtureDirPath . '/init.xml');
    }

    public function getDoctrine2Tagging()
    {
        $options = self::$doctrineOptions;
        $tagging = new Doctrine2Tagging($options);
        $cache = new \Poc\Cache\CacheImplementation\FileCache(array(\Poc\Cache\CacheImplementation\CacheParams::PARAM_TTL => $GLOBALS['TTL']));
        $poc = new \Poc\Poc(array(\Poc\PocParams::PARAM_CACHE => $cache));
        $tagging->init($poc);

        return $tagging;
    }

    /**
     * @dataProvider addCacheToTagsProvider
     */
    public function testAddCacheToTags ($tagsString, $expectedDatasetFilename)
    {
        $expectedDatasetPath = $this->fixtureDirPath. '/addCacheToTags/' . $expectedDatasetFilename;

        $tagging = $this->getDoctrine2Tagging();

        $tagging->addCacheToTags($tagsString);
        $dataSet = $this->getConnection()->createDataSet(
                array('caches', 'tags_has_caches', 'tags'));

        $query = 'UPDATE caches SET expires = 1234';
        self::$pdo->exec($query);

        $exepctedDataSet = $this->createXMLDataSet(
                $expectedDatasetPath . '-01.xml');
        $this->assertDataSetsEqual($exepctedDataSet, $dataSet);

        sleep($GLOBALS['TTL']+1);
        $tagging->addCacheToTags($tagsString);

        $query = 'UPDATE caches SET expires = 1234';
        self::$pdo->exec($query);

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
    public function testFlushOutdated ($initDatasetFilename, $expectedDatasetFilename)
    {
        $initDatasetPath = $this->fixtureDirPath.'/flushOutdated/' . $initDatasetFilename;
        $expectedDatasetPath = $this->fixtureDirPath.'/flushOutdated/' . $expectedDatasetFilename;

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
    public function testTagInvalidate ($initDatasetFilename, $invalidateTag, $expectedDatasetFilename)
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
        $data = array(array('tag1,tag2', 'expected_01'));

        return $data;
    }

    public static function flushOutdatedProvider ()
    {
        $data = array(array('init_01.xml', 'expected_01.xml'));

        return $data;
    }

    public static function tagInvalidateProvider ()
    {
        $data = array(
            array('init_01.xml', 'tag1', 'expected_01-01.xml'),
            array('init_01.xml', 'tag2', 'expected_01-02.xml'),
            array('init_01.xml', 'tag3', 'expected_01-03.xml'),
            array('init_01.xml', 'tag1,tag2', 'expected_01-04.xml'),
            array('init_01.xml', 'tag1,tag3', 'expected_01-05.xml'),
            array('init_01.xml', 'tag2,tag3', 'expected_01-06.xml'),
            array('init_01.xml', 'tag1,tag2,tag3', 'expected_01-07.xml')
        );

        return $data;
    }
}
