<?php
/*
 * Copyright 2012 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

namespace Poc\PocPlugins\Tagging;

use Doctrine\ORM\EntityManager;
use Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Cache;
use Poc\PocPlugins\Tagging\AbstractDb;
use Poc\Optionable\DoctrineOptionable;

class Doctrine2Tagging extends AbstractDb
{
    protected $entityManager;

    protected $entitiesNamespaceString = 'Poc\\PocPlugins\\Tagging\\Driver\\Doctrine2\\Entities';

    public function __construct ($options = array())
    {
        $this->optionable = new DoctrineOptionable($options);
        $this->entityManager = $this->optionable['orm.entity_managers.default'];
        parent::__construct();
    }

    public function addCacheToTags ($tagNamesString)
    {
        $expires = time()+$this->ttl;

        $entityManager = $this->getEntityManager();
        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();

        $cache = $cacheRepository->findOneBy(array('hash' => $this->hash));

        if ($cache) {

            // cache exists
            if ($cache->getExpires() > time()) {

                // cache is not expired it will be renewed
                $cache->setExpires($expires);
                $entityManager->persist($cache);
                $entityManager->flush();
            } else {
                // cache is expired it will be deleted with relation table item
                // $cache->getTags()->clear();
                $entityManager->remove($cache);
                $entityManager->flush();

                $cache = null;
            }
        }

        if (! $cache) {
            $entityManager->clear();
            $cache = new Cache();
            $cache->setHash($this->hash);
            $cache->setExpires($expires);
            $entityManager->persist($cache);
            $entityManager->flush();
        }

        $tagNames = $this->splitTags($tagNamesString);

        $tags = array();
        foreach ($tagNames as $tagName) {
            $cachesOfTag = $cacheRepository->getByTagAndHash($tagName,
                    $cache->getHash());

            if (! $cachesOfTag) {
                $tag = $tagRepository->findOneBy(array('tag' => $tagName));

                if (! $tag) {
                    $tag = new \Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Tag();
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

    public function flushOutdated ()
    {
        $entityManager = $this->getEntityManager();
        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();
        $cacheTagRepository = $this->getCacheTagRepository();

        $this->deleteOrphans();

        $expiredTagsCaches = $cacheTagRepository->getExpired();

        foreach ($expiredTagsCaches as $expiredTagCache) {
            if ($cache = $expiredTagCache->getCache()) {
                $entityManager->remove($cache);
            }
            if ($tag = $expiredTagCache->getTag())
                $entityManager->remove($tag);
        }
        $entityManager->flush();
    }

    public function tagInvalidate ($tagsString)
    {
        $entityManager = $this->getEntityManager();

        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();

        $tagNames = $this->splitTags($tagsString);

        $invalidateCaches = $cacheRepository->getByTags($tagNames);

        foreach ($invalidateCaches as $invalidateCache) {
            $this->cache->clearItem($this->poc->getHasher()->getKey());
            $entityManager->remove($invalidateCache);
            $entityManager->flush();
        }

        $tagRepository->removeByNames($tagNames);

    }

    protected function deleteOrphans ()
    {
        return;
        $cacheRepository = $this->getCacheRepository();
        $tagRepository = $this->getTagRepository();
        $cacheTagRepository = $this->getCacheTagRepository();

        $cacheRepository->orphanRemoval();
        $tagRepository->orphanRemoval();
        $cacheTagRepository->orphanRemoval();

    }

    protected function getCacheRepository ()
    {
        return $this->getEntityManager()->getRepository(
                $this->entitiesNamespaceString . '\\Cache');
    }

    protected function getCacheTagRepository ()
    {
        return $this->getEntityManager()->getRepository(
                $this->entitiesNamespaceString . '\\CacheTag');
    }

    protected function getEntityManager ()
    {
        $entityManager = $this->entityManager;

        return $entityManager;
    }

    protected function getTagRepository ()
    {
        return $this->getEntityManager()->getRepository(
                $this->entitiesNamespaceString . '\\Tag');
    }

    protected function splitTags ($tags)
    {
        return explode(',', $tags);
    }

}
