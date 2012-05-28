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

namespace Poc\Cache\Tagging;

use Poc\Cache\Tagging\Driver\Mysql\Cache;
use Poc\Cache\Tagging\Driver\Mysql\Tag;
use Poc\Cache\Tagging\Driver\Mysql\TagCache;
use Poc\Cache\Tagging\Driver\Mysql\CacheModelManager;
use Poc\Cache\Tagging\Driver\Mysql\TagModelManager;
use Poc\Cache\Tagging\Driver\Mysql\TagsHasCachesModelManager;
use Poc\Cache\Tagging\AbstractDb;

class MysqlTagging extends AbstractDb
{

    const DEFDB = 'poc_tagging';

    const DEFHOST = 'localhost';

    const DEFUSER = 'root';

    const DEFPASS = 'root';

    private $db;

    private $host;

    private $user;

    private $pass;

    private $link;

    private $tagIDs = array();

    private $tryOfCon = 0;

    private $PDO;

    private $cmm = null;

    private $tmm = null;

    private $tcmm = null;

    public function __construct ($db = self::DEFDB, $host = self::DEFHOST, $user = self::DEFUSER, $pass = self::DEFPASS)
    {

        $this->db = $db;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;

        $this->dsn = 'mysql:dbname=' . $db . ';host=' . $host;
        $this->connectDb();

        parent::__construct();

    }

    protected function connectDb ()
    {
        $this->PDO = new \PDO($this->dsn, $this->user, $this->pass);
        $this->cmm = new CacheModelManager($this->PDO);
        $this->tmm = new TagModelManager($this->PDO);
        $this->tcmm = new TagsHasCachesModelManager($this->PDO);
        
        return true;
    }

    public function splitTags ($tags)
    {
        return explode(',', $tags);
    }

    /*
     * If cache exists and it is not expired it will be added to tags and be
     * renewed expires value. If cache exists and it is expired it will be
     * deleted and it will be inserted again If cache doesn't exist it will be
     * inserted
     */

    public function addCacheToTags ($tagNamesString, $hash, $expires = null)
    {
        if (! $expires) {
            $expires = time();
        }
        // $expires = time()+$ttl;
        $cache = $this->cmm->findOneBy('hash', $hash);

        $isNewCache = false;
        $isCacheRenew = false;

        if ($cache) {
            // cache exists
            if ($cache->expires > time()) {
                // cache is not expired it will be renewed
                $isCacheRenew = true;
                $cache->expires = $expires;
                $this->cmm->save($cache);
            } else {
                // cache is expired it will be deleted with relation table item
                $this->cmm->deleteWithRelation($cache->hash);
                $cache = null;
            }
        }

        if (! $cache) {
            $isNewCache = true;
            $cache = new Cache();
            $cache->hash = $hash;
            $cache->expires = $expires;
            $this->cmm->save($cache);
        }

        $tagNames = $this->splitTags($tagNamesString);

        $tags = array();
        $tagsCaches = array();
        foreach ($tagNames as $tagName) {
            $cachesOfTag = $this->cmm->getByTagAndHash($tagName, $cache->hash);

            if (! $cachesOfTag) {
                $tag = $this->tmm->findOneBy('tag', $tagName);

                if (! $tag) {
                    $tag = new Tag();
                    $tag->tag = $tagName;
                    $this->tmm->save($tag);
                }
                $tagCache = new TagCache();
                $tagCache->cache_id = $cache->id;
                $tagCache->tag_id = $tag->id;
                $tagsCaches[] = $tagCache;
                $tags[] = $tag;
            }

        }

        $this->tcmm->save($tagsCaches);
    }

    public function tagInvalidate ($tagsString)
    {
        $tagNames = $this->splitTags($tagsString);

        $invalidateCaches = $this->cmm->getByTags($tagNames);

        foreach ($invalidateCaches as $invalidateCache) {
            $this->cmm->deleteWithRelation($invalidateCache->hash);
            $this->cache->clearItem($invalidateCache->hash);

        }
    }

    public function flushOutdated ()
    {
        $this->deleteOrphans();
        $expiredTagsCaches = $this->tcmm->getExpired();

        foreach ($expiredTagsCaches as $expiredTagCache) {
            $this->tcmm->delete($expiredTagCache);
            $this->cmm->delete($expiredTagCache->cache_id);
            $this->tmm->delete($expiredTagCache->tag_id);
        }

    }

    public function deleteOrphans ()
    {
        $this->tcmm->deleteOrphans();
        $this->cmm->deleteOrphans();
        $this->tmm->deleteOrphans();

    }

}
