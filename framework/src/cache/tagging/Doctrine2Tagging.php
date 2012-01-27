<?php
/*Copyright 2012 Imre Toth <tothimre at gmail>

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

namespace POC\cache\tagging;

use Doctrine\ORM\EntityManager, Doctrine\ORM\Configuration;

require_once 'Doctrine/Common/ClassLoader.php';

class Doctrine2Tagging extends AbstractDb
{

    const DEFDB = 'poc_tagging';

    const DEFHOST = 'localhost';

    const DEFUSER = 'poc_test';

    const DEFPASS = 'poc_test';

    protected $entityManager;
    
    public function __construct ($db = self::DEFDB, $host = self::DEFHOST, $user = self::DEFUSER, $pass = self::DEFPASS)
    {
        $connectionOptions = array(
        		'driver'   => 'pdo_mysql',
        		'path'     => $host,
        		'dbname'   => $db,
        		'user'     => $user,
        		'password' => $pass
        );
        

        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
        $classLoader->register();
        
        $config = new Configuration;
        //        $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        
        
        $proxyDirPath = __DIR__.'/../../../../tmp/doctrine2/Proxies';

        if (!is_dir($proxyDirPath))
        {
            mkdir($proxyDirPath, 0700, true);
        }
        
        $config->setProxyDir($proxyDirPath);
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(true);

        $driverImpl = $config->newDefaultAnnotationDriver(__DIR__.'/driver/doctrine2/Entities');
        $config->setMetadataDriverImpl($driverImpl);
        
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        
        $this->entityManager = EntityManager::create($connectionOptions, $config);
        
        parent::__construct();
    }


    public function addCacheToTags ($tagNamesString, $hash, $expires = null)
    {
        if (!$expires)
        {
        	$expires = time();
        }
        
        $entityManager = $this->getEntityManager();
        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();
        
        $cache = $cacheRepository->findOneBy(array('hash' => $hash));
        

        if ($cache)
        {
        	// cache exists
        	if ($cache->getExpires() > time())
        	{
        		// cache is not expired it will be renewed
        		$cache->setExpires($expires);
        		$entityManager->persist($cache);
        		$entityManager->flush();
        	}
        	else
        	{
        		// cache is expired it will be deleted with relation table item
//        	    $cache->getTags()->clear();
        	    $entityManager->remove($cache);
        	    $entityManager->flush();
        	    $cache = null;
        	     
        	}
        }
        
        if (!$cache)
        {
            $entityManager->clear();
        	$cache = new \Entities\Cache();
        	$cache->setHash($hash);
        	$cache->setExpires($expires);
        	$entityManager->persist($cache);
        	$entityManager->flush();
        	 
        }
        
        $tagNames = $this->splitTags($tagNamesString);
        
        $tags = array();
        $tagsCaches = array();
        foreach($tagNames as $tagName)
        {
            $cachesOfTag = $cacheRepository->getByTagAndHash($tagName, $cache->getHash());
            
        	if (!$cachesOfTag)
        	{
        		$tag = $tagRepository->findOneBy(array('tag' => $tagName));
        		
        		if (!$tag)
        		{
        		    $tag = new \Entities\Tag();
        		    $tag->setTag($tagName);
        		    $entityManager->persist($tag);
        		    $entityManager->flush();
        		    
        		}
                $cache->addTag($tag);
        		$entityManager->flush();
        		
        	}
        
        }
        
        return $cache;
    }
    
	
    
    public function flushOutdated()
    {
        $entityManager = $this->getEntityManager();
        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();
        $cacheTagRepository = $this->getCacheTagRepository();
        

        
    	$this->deleteOrphans();
            	 
    	$expiredTagsCaches = $cacheTagRepository->getExpired();

    	foreach($expiredTagsCaches as $expiredTagCache)
    	{
    	    if ($cache = $expiredTagCache->getCache())
    	    {
    	        $entityManager->remove($cache);
    	    }
    	    if ($tag = $expiredTagCache->getTag())
    	    $entityManager->remove($tag);
    	}
    	$entityManager->flush();
    }
    
    
    
    public function tagInvalidate($tagsString)
    {
        $entityManager = $this->getEntityManager();
        
        $cacheRepository = $this->getCacheRepository();

    	$tagNames = $this->splitTags($tagsString);
    
    	$invalidateCaches = $cacheRepository->getByTags($tagNames);
    
    	foreach ($invalidateCaches as $invalidateCache)
    	{
    	    $entityManager->remove($invalidateCache);
    
    	}
    }

    
    
    protected function createDb ()
    {
    }
    
    protected function createTables ()
    {
    }

    protected function deleteOrphans()
    {
    	$cacheRepository = $this->getCacheRepository();
    	$tagRepository = $this->getTagRepository();
    	$cacheTagRepository = $this->getCacheTagRepository();
    
    	$cacheRepository->orphanRemoval();
    	$tagRepository->orphanRemoval();
    	$cacheTagRepository->orphanRemoval();
    
    }

    protected function getCacheRepository() {
    	return $this->getEntityManager()->getRepository('Entities\\Cache');
    }

    protected function getCacheTagRepository() {
    	return $this->getEntityManager()->getRepository('Entities\\CacheTag');
    }

    protected function getEntityManager() {
    	$entityManager = $this->entityManager;
    	return $entityManager;
    }
    
    protected function getTagRepository() {
    	return $this->getEntityManager()->getRepository('Entities\\Tag');
    }
    
    
    protected function splitTags($tags){
    	return explode(',',$tags);
    }
    
    protected function initDbStructure ()
    {
    }
    
}
?>