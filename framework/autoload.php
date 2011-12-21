<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart 
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'abstractdb' => '/src/cache/tagging/AbstractDb.php',
                'abstractpoccachespecific' => '/src/cache/cacheImplementation/AbstractPocCacheSpecific.php',
                'filecache' => '/src/cache/cacheImplementation/FileCache.php',
                'logger' => '/src/utility/Logger.php',
                'memcachedcache' => '/src/cache/cacheImplementation/MemcachedCache.php',
                'mongocache' => '/src/cache/cacheImplementation/MongoDBCache.php',
                'mysqltagging' => '/src/cache/tagging/MysqlTagging.php',
                'poc\\cache\\filtering\\evaluateable' => '/src/cache/filtering/Evaluateable.php',
                'poc\\cache\\filtering\\hasvalue' => '/src/cache/filtering/HasValue.php',
                'poc\\cache\\filtering\\tohash' => '/src/cache/filtering/ToHash.php',
                'poc\\cache\\filtering\\tostring' => '/src/cache/filtering/ToString.php',
                'poc\\cache\\poccache' => '/src/cache/PocCache.php',
                'poc\\cache\\poccacheinterface' => '/src/cache/PocCacheInterface.php',
                'poc\\core\\optioner' => '/src/core/Optioner.php',
                'poc\\handlers\\outputinterface' => '/src/handlers/OutputInterface.php',
                'poc\\handlers\\serveroutput' => '/src/handlers/ServerOutput.php',
                'poc\\poc' => '/src/Poc.php',
                'poccachespecificinterface' => '/src/cache/cacheImplementation/PocCacheSpecificInterface.php',
                'rediskacache' => '/src/cache/cacheImplementation/RediskaCache.php',
                'sqlitetagging' => '/src/cache/tagging/SqliteTagging.php',
                'tagger' => '/src/cache/tagging/Tagger.php',
                'unittest\\cachetest' => '/tests/cache/cacheImplmentation/CacheTest.php',
                'unittest\\filecachetest' => '/tests/cache/cacheImplmentation/FileCacheTest.php',
                'unittest\\handler\\testoutput' => '/tests/handlers/TestOutput.php',
                'unittest\\memcachedcachetest' => '/tests/cache/cacheImplmentation/MemcachedCacheTest.php',
                'unittest\\mongocachetest' => '/tests/cache/cacheImplmentation/MongoCacheTest.php',
                'unittest\\poctest' => '/tests/PocTest.php',
                'unittest\\rediskacachetest' => '/tests/cache/cacheImplmentation/RediskaCacheTest.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd