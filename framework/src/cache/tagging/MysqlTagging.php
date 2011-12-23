<?php
class MysqlTagging extends AbstractDb {

  const DEFDB = 'PobTagging';
  const DEFHOST = 'localhost';
  const DEFUSER = 'root';
  const DEFPASS = 'root';

  private $db;
  private $host;
  private $user;
  private $pass;
  private $link;
  private $tagIDs = array();

  function __construct($db = self::DEFDB, $host = self::DEFHOST,
                                 $user = self::DEFUSER, $pass = self::DEFPASS) {

    $this->db = $db;
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;
    parent::__construct();
  }

  function checkDb(){
    $this->link = mysql_connect($this->host, $this->user, $this->pass);
    if (!$this->link) {
      die("PLEASE ADD PROPER DATABASE RIGHTS FOR YOUR POC INSTANCES MysqlTagging class!");
    }
    return true;
  }

  protected function openDb(){
    $selectedDB = mysql_select_db($this->db, $this->link);
    if (!$selectedDB) {
     return false;
    }
    return true;
  }

  function createDb(){
    $query = 'CREATE DATABASE `'.$this->db.'` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci';
    mysql_query($query, $this->link);

    $this->openDb();

    $query ='CREATE TABLE IF NOT EXISTS `caches` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `hash` char(64) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
    mysql_query($query, $this->link);

    $query = 'CREATE TABLE IF NOT EXISTS `tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag` char(10) COLLATE utf8_bin NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
    mysql_query($query, $this->link);

    $query = 'CREATE TABLE IF NOT EXISTS `tags_has_caches` (
    `tag_id` int(11) NOT NULL,
    `cache_id` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
    mysql_query($query, $this->link);

  }

  private function fetchArray($query){
    $result = mysql_query($query);
    $return = array();
    while($row = mysql_fetch_array($result)){
      $return[] = $row;
    }
    return $return;
  }

  protected function addTags($tags) {
    $tagArray = $this->splitTags($tags);
    $tagIds;
    foreach($tagArray as $tag){
      $query = 'SELECT id FROM tags where tag = "'.$tag.'"';
      $row = $this->fetchArray($query);
      if(!$row){
        mysql_query('INSERT INTO tags VALUES (null, "'.$tag.'")');
        echo(mysql_error($this->link));
        $tagIds[$tag] = mysql_insert_id($this->link);;
      } else {
        $tagIds[$tag] = $row[0]['id'];
      }
    }
    return($tagIds);
  }


  function addCacheToTags($tags, $cacheKey) {
    $tagIds = $this->addTags($tags);
    foreach($tagIds as $tagId){
      $query = 'SELECT id FROM caches where hash = "'.$cacheKey.'"';
      $result = $this->fetchArray($query);
      $cacheId;
      if(!$result){
         mysql_query('INSERT INTO caches VALUES (null, "'.$cacheKey.'")');
         $cacheId = $this->base->lastInsertRowID();
      }
      else{
        $cacheId = $result[0]['id'];
      }
      $query = 'SELECT * FROM tags_has_caches where tagID = "'.$tagId.
                                          '" AND cacheID="'.$cacheId.'"';
      $row = $this->fetchArray($query);
      if(!$row){
        $query = 'INSERT INTO tags_has_caches
                  VALUES("'.$tagId.'", "'.$cacheId.'")';
        mysql_query($query);
      }
    }
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

  function splitTags($tags){
    return explode(',',$tags);
  }

  function flushOutdated() {
  }

  function dbinfo(){
        $query = 'SELECT * FROM tags';
 //       $result = $this->base->query($query);
   //     $rowsa = $result->fetchAll();

        $query = 'SELECT * FROM caches';
     //   $result = $this->base->query($query);
       // $rowsb = $result->fetchAll();

        $query = 'SELECT * FROM tags_has_caches';
   //     $result = $this->base->query($query);
     //   $rowsc = $result->fetchAll();
  }
}
