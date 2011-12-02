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
                'apccache' => '/src/cache/cacheImplementation/ApcCache.php',
                'filecache' => '/src/cache/cacheImplementation/FileCache.php',
                'logger' => '/src/utility/Logger.php',
                'memcachedcache' => '/src/cache/cacheImplementation/MemcachedCache.php',
                'poc\\cache\\filtering\\evaluateable' => '/src/cache/filtering/Evaluateable.php',
                'poc\\cache\\filtering\\hasvalue' => '/src/cache/filtering/HasValue.php',
                'poc\\cache\\filtering\\tohash' => '/src/cache/filtering/ToHash.php',
                'poc\\cache\\filtering\\tostring' => '/src/cache/filtering/ToString.php',
                'poc\\handlers\\outputinterface' => '/src/handlers/OutputInterface.php',
                'poc\\handlers\\serveroutput' => '/src/handlers/ServerOutput.php',
                'poc\\poc' => '/src/Poc.php',
                'poccache' => '/src/cache/PocCache.php',
                'poccacheinterface' => '/src/cache/PocCacheInterface.php',
                'poccachespecificinterface' => '/src/cache/cacheImplementation/PocCacheSpecificInterface.php',
                'sqlitetagging' => '/src/cache/tagging/SqliteTagging.php',
                'tagger' => '/src/cache/tagging/Tagger.php',
                'unittest\\handler\\testoutput' => '/tests/handlers/TestOutput.php',
                'unittest\\testclasstest' => '/tests/PocTest.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd