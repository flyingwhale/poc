<?php // this is an autogenerated file - do not edit (created Sat, 16 Jul 2011 22:53:09 +0200)
spl_autoload_register(
   function($class) {
      static $classes = null;
      if ($classes === null) {
         $classes = array(
            'abstractdb' => '/src/cache/tagging/AbstractDb.php',
            'abstractpobcachespecific' => '/src/cache/cacheImplementation/AbstractPobCacheSpecific.php',
            'apccache' => '/src/cache/cacheImplementation/ApcCache.php',
            'filecache' => '/src/cache/cacheImplementation/FileCache.php',
            'logger' => '/src/utility/Logger.php',
            'memcachedcache' => '/src/cache/cacheImplementation/MemcachedCache.php',
            'outputinterface' => '/src/handlers/OutputInterface.php',
            'pobcache' => '/src/cache/PobCache.php',
            'pobcacheinterface' => '/src/cache/PobCacheInterface.php',
            'pobcachespecificinterface' => '/src/cache/cacheImplementation/PobCacheSpecificInterface.php',
            'poc\\cache\\filtering\\evaluateable' => '/src/cache/filtering/Evaluateable.php',
            'poc\\cache\\filtering\\hasvalue' => '/src/cache/filtering/HasValue.php',
            'poc\\cache\\filtering\\tohash' => '/src/cache/filtering/ToHash.php',
            'poc\\cache\\filtering\\tostring' => '/src/cache/filtering/ToString.php',
            'poc\\pob' => '/src/Pob.php',
            'serveroutput' => '/src/handlers/ServerOutput.php',
            'sqlitetagging' => '/src/cache/tagging/SqliteTagging.php',
            'tagger' => '/src/cache/tagging/Tagger.php',
            'unittest\\handler\\testoutput' => '/tests/handlers/TestOutput.php',
            'unittest\\testclasstest' => '/tests/PobTest.php'
          );
      }
      $cn = strtolower($class);
      if (isset($classes[$cn])) {
         require __DIR__ . $classes[$cn];
      }
   }
);