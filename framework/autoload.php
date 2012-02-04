<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'entities\\cache' => '/src/cache/tagging/driver/doctrine2/Entities/Cache.php',
                'entities\\cachetag' => '/src/cache/tagging/driver/doctrine2/Entities/CacheTag.php',
                'entities\\tag' => '/src/cache/tagging/driver/doctrine2/Entities/Tag.php',
                'logger' => '/src/utility/Logger.php',
                'pimple' => '/vendors/pimple/lib/Pimple.php',
                'pimple\\tests\\pimpletest' => '/vendors/pimple/tests/Pimple/Tests/PimpleTest.php',
                'pimple\\tests\\service' => '/vendors/pimple/tests/Pimple/Tests/Service.php',
                'poc\\cache\\cacheimplementation\\cache' => '/src/cache/cacheimplementation/Cache.php',
                'poc\\cache\\cacheimplementation\\cacheinterface' => '/src/cache/cacheimplementation/CacheInterface.php',
                'poc\\cache\\cacheimplementation\\cacheparams' => '/src/cache/cacheimplementation/CacheParams.php',
                'poc\\cache\\cacheimplementation\\filecache' => '/src/cache/cacheimplementation/FileCache.php',
                'poc\\cache\\cacheimplementation\\memcachedcache' => '/src/cache/cacheimplementation/MemcachedCache.php',
                'poc\\cache\\cacheimplementation\\mongocache' => '/src/cache/cacheimplementation/MongoDBCache.php',
                'poc\\cache\\cacheimplementation\\rediskacache' => '/src/cache/cacheimplementation/RediskaCache.php',
                'poc\\cache\\cacheinvalidationprotection\\ciaprotector' => '/src/cache/cacheInvalidationProtection/CIAProtector.php',
                'poc\\cache\\filtering\\filter' => '/src/cache/filtering/Filter.php',
                'poc\\cache\\filtering\\hasher' => '/src/cache/filtering/Hasher.php',
                'poc\\cache\\filtering\\outputfilter' => '/src/cache/filtering/OutputFilter.php',
                'poc\\cache\\header\\headermanipulator' => '/src/cache/header/HeaderManipulator.php',
                'poc\\cache\\tagging\\abstractdb' => '/src/cache/tagging/AbstractDb.php',
                'poc\\cache\\tagging\\doctrine2tagging' => '/src/cache/tagging/Doctrine2Tagging.php',
                'poc\\cache\\tagging\\driver\\mysql\\cachemodelmanager' => '/src/cache/tagging/driver/mySQL/CacheModelManager.php',
                'poc\\cache\\tagging\\driver\\mysql\\model\\cache' => '/src/cache/tagging/driver/mySQL/Cache.php',
                'poc\\cache\\tagging\\driver\\mysql\\model\\tag' => '/src/cache/tagging/driver/mySQL/Tag.php',
                'poc\\cache\\tagging\\driver\\mysql\\model\\tagcache' => '/src/cache/tagging/driver/mySQL/TagCache.php',
                'poc\\cache\\tagging\\driver\\mysql\\modelmanager' => '/src/cache/tagging/driver/mySQL/ModelManager.php',
                'poc\\cache\\tagging\\driver\\mysql\\tagmodelmanager' => '/src/cache/tagging/driver/mySQL/TagModelManager.php',
                'poc\\cache\\tagging\\driver\\mysql\\tagshascachesmodelmanager' => '/src/cache/tagging/driver/mySQL/TagsHasCachesModelManager.php',
                'poc\\cache\\tagging\\mysqltagging' => '/src/cache/tagging/MysqlTagging.php',
                'poc\\cache\\tagging\\tagger' => '/src/cache/tagging/Tagger.php',
                'poc\\core\\optionable' => '/src/core/OptionAble.php',
                'poc\\core\\optionableinterface' => '/src/core/OptionAbleInterface.php',
                'poc\\handlers\\outputinterface' => '/src/handlers/OutputInterface.php',
                'poc\\handlers\\serveroutput' => '/src/handlers/ServerOutput.php',
                'poc\\poc' => '/src/Poc.php',
                'poc\\pocparams' => '/src/PocParams.php',
                'repositories\\cacherepository' => '/src/cache/tagging/driver/doctrine2/Repositories/CacheRepository.php',
                'repositories\\cachetagrepository' => '/src/cache/tagging/driver/doctrine2/Repositories/CacheTagRepository.php',
                'repositories\\tagrepository' => '/src/cache/tagging/driver/doctrine2/Repositories/TagRepository.php',
                'unittest\\cachetest' => '/tests/cache/cacheImplmentation/CacheTest.php',
                'unittest\\doctrine2taggingtest' => '/tests/cache/tagging/Doctrine2TaggingTest.php',
                'unittest\\filecachetest' => '/tests/cache/cacheImplmentation/FileCacheTest.php',
                'unittest\\handler\\testoutput' => '/tests/handlers/TestOutput.php',
                'unittest\\memcachedcachetest' => '/tests/cache/cacheImplmentation/MemcachedCacheTest.php',
                'unittest\\mockoptionableinterfaceclass' => '/tests/core/OptionAbleTest.php',
                'unittest\\mongocachetest' => '/tests/cache/cacheImplmentation/MongoCacheTest.php',
                'unittest\\mysqltaggingtest' => '/tests/cache/tagging/MysqlTaggingTest.php',
                'unittest\\optionabletest' => '/tests/core/OptionAbleTest.php',
                'unittest\\poctest' => '/tests/PocTest.php',
                'unittest\\rediskacachete' => '/tests/cache/cacheImplmentation/RediskaCacheTest.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd