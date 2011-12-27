<?php
namespace POC\cache\tagging;

use POC\cache\tagging\driver\mySQL\model\Cache;
use POC\cache\tagging\driver\mySQL\model\Tag;
use POC\cache\tagging\driver\mysql\model\TagCache;
use POC\cache\tagging\driver\mySQL\CacheModelManager;
use POC\cache\tagging\driver\mySQL\TagModelManager;
use POC\cache\tagging\driver\mySQL\TagsHasCachesModelManager;


class MysqlTagging extends AbstractDb {

  const DEFDB   = 'pob_tagging';
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

  function __construct($db = self::DEFDB, $host = self::DEFHOST,
  $user = self::DEFUSER, $pass = self::DEFPASS) {

    $this->db   = $db;
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;

    $this->dsn = 'mysql:dbname='.$db.';host='.$host;
    if ($this->tryOfCon < 1)
    {
      $this->connectDb();
    }
    else
    {
      throw new Exception('Mysql database connection failed.');
    }
  }

  protected function connectDb()
  {
    try {
      $this->PDO = new \PDO($this->dsn, $this->user, $this->pass);
    }
    catch(\PDOException $Exception) {
      $this->tryOfCon++;
      if ($Exception->getCode() == 1049)
      {
        $dsn = 'mysql:;host='.$this->host;
        $this->PDO = new \PDO($dsn, $this->user, $this->pass);
        $this->createDb();
        $this->cmm = new CacheModelManager($this->PDO);
        $this->tmm = new TagModelManager($this->PDO);
        $this->tcmm = new TagsHasCachesModelManager($this->PDO);
        $this->createTables();
      }
    }
    $this->cmm = new CacheModelManager($this->PDO);
    $this->tmm = new TagModelManager($this->PDO);
    $this->tcmm = new TagsHasCachesModelManager($this->PDO);

  }

  protected function initDbStructure()
  {
    if ($this->createDb())
    {
      $this->createTables();
    }
  }

  protected function createDb()
  {
    $query = 'CREATE DATABASE `'.$this->db.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
    $this->PDO->exec($query) or
    die("PLEASE ADD PROPER DATABASE RIGHTS FOR YOUR POC INSTANCES MysqlTagging class!");

    $query = 'USE '.$this->db;
    $this->PDO->exec($query);
  }

  protected function createTables()
  {
    $this->cmm->createTable();
    $this->tmm->createTable();
    $this->tcmm->createTable();
  }

  function splitTags($tags){
    return explode(',',$tags);
  }

  function addCacheToTags($tagNamesString, $hash, $ttl = 5)
  {
    $expires = time()+$ttl;
    $cache = $this->cmm->findOneBy('hash', $hash);

    $isNewCache = false;
    $isCacheRenew = false;

    if (!$cache)
    {
      $isNewCache = true;
      $cache = new Cache();
      $cache->hash = $hash;
      $cache->expires = $expires;
      $this->cmm->save($cache);
    }
    else {
      if ($cache->expires < time())
      {
        $isCacheRenew = true;
        $cache->expires = $expires;
        $this->cmm->save($cache);
      }
    }

    $tagNames = $this->splitTags($tagNamesString);

    $tags = array();
    $tagsCaches = array();
    foreach($tagNames as $tagName)
    {
      $tag = $this->tmm->findOneBy('tag', $tagName);

      if (!$tag)
      {
        $tag = new Tag();
        $tag->tag = $tagName;
        $this->tmm->save($tag);

        $tagCache = new TagCache();
        $tagCache->cache_id = $cache->id;
        $tagCache->tag_id = $tag->id;
        $tagsCaches[] = $tagCache;
      }

      $tags[] = $tag;
    }

    $this->tcmm->save($tagsCaches);
  }
  private function fetchArray($query){
    $result = mysql_query($query);
    $return = array();
    while($row = mysql_fetch_array($result)){
      $return[] = $row;
    }
    return $return;
  }


  function tagInvalidate($tags) {
    $tagArray = $this->splitTags($tags);
    if($tagArray){
      $tagsWhere = '';
      foreach($tagArray as $index => $tag){
        if($index){
          $tagsWhere .= 'or ';
        }
        $tagsWhere .= 'tag = "'.$tag.'" ';
      }
      $query = 'select ID from tags where '.$tagsWhere;
      $rows = $this->fetchArray($query);

      $tags_has_cachesWhere = '';
      if($rows){
        foreach($rows as $index => $row){
          if($index){
            $tags_has_cachesWhere .= 'or ';
          }
          $tags_has_cachesWhere .= 'tagID = "'.$row['id'].'" ';
        }
        $query = 'SELECT cacheID FROM tags_has_caches WHERE '.$tags_has_cachesWhere.'GROUP BY cacheID';
        $rows = $this->fetchArray($query);
        if($rows){
          $cacheWhere = '';
          foreach($rows as $index => $row){
            if($index){
              $cacheWhere .='or ';
            }
            $cacheWhere .='id = "'.$row['cache_id'].'" ';
          }
          $query = 'SELECT hash FROM caches WHERE '.$cacheWhere;
          $rows = $this->fetchArray($query);

          foreach($rows as $row){
            $this->cache->cacheSpecificClearItem($row['hash']);
          }
        }
      }
    }
  }


  function flushOutdated() {
  }

}
