<?php
class MysqlTagging extends AbstractDb {

  private $db;
  private $host;
  private $user;
  private $pass;
  private $link;
  private $tagIDs = array();

  function __construct($db='localhost',$host='localhost',$user='root',$pass='root') {
    $this->db = $db;
    $this->host = $localhost;
    $this->user = $user;
    $this->pass = $pass;
    parent::__construct();
  }

  function checkDb(){
    $this->link = mysql_connect($this->host, $this->user, $this->pass);
    if (!$this->link) {
      die("PLEASE ADD PROPER DATABASE RIGHTS FOR YOUR POC INSTANCES MysqlTagging class!")
    }
    $ret = 1;
    return $ret;
  }

  private function openDb(){
    $selectedDB = mysql_select_db($this->db, $link);
    if (!$selectedDb) {
     return false
    }
    return true;
  }

  function createDb(){
    $query = 'CREATE TABLE tags(
              ID INTEGER PRIMARY KEY,
              tag CHARACTER(32)
              )';
    $this->base->queryexec($query);

    $query = 'CREATE TABLE tags_has_caches(
              tagID INTEGER,
              cacheID INTEGER
              )';
    $this->base->queryexec($query);

    $query = 'CREATE TABLE caches(
              ID INTEGER PRIMARY KEY,
              hash_key CHARACTER(32)
              )';
    $this->base->queryexec($query);
  }

  protected function addTags($tags) {
    $tagArray = $this->splitTags($tags);
    $tagIds;
    foreach($tagArray as $tag){
      $query = 'SELECT ID FROM tags where tag = "'.$tag.'"';
      $result = $this->base->query($query);
      $row = $result->fetch();
      if(!$row){
        $this->base->queryexec('INSERT INTO tags VALUES (null, "'.$tag.'")');
        $tagIds[$tag] = $this->base->lastInsertRowID();
      } else {
        $tagIds[$tag] = $row[0]['ID'];
      }
    }
    return($tagIds);
  }


  function addCacheToTags($tags, $cacheKey) {
    $tagIds = $this->addTags($tags);
    foreach($tagIds as $tagId){
      $query = 'SELECT ID FROM caches where hash_key = "'.$cacheKey.'"';
      $result = $this->base->query($query);
      $rowCache = $result->fetch();
      $cacheId;
      if(!$rowCache){
        $this->base->query('INSERT INTO caches VALUES (null, "'.$cacheKey.'")');
        $cacheId = $this->base->lastInsertRowID();
      }
      else{
        $cacheId = $rowCache['ID'];
      }
      $query = 'SELECT * FROM tags_has_caches where tagID = "'.$tagId.
                                                                   '" AND cacheID="'.$rowCache['ID'].'"';
      $result = $this->base->query($query);
      $row = null;
      $row = $result->fetch();
      if(!$row){
        $query = 'INSERT INTO tags_has_caches
                  VALUES("'.$tagId.'", "'.$cacheId.'")';
        $this->base->queryexec($query);
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
      $result = $this->base->query($query);
      $rows = null;
      $rows = $result->fetchAll();

      $tags_has_cachesWhere = '';
      if($rows){
        foreach($rows as $index => $row){
          if($index){
            $tags_has_cachesWhere .= 'or ';
          }
          $tags_has_cachesWhere .= 'tagID = "'.$row['ID'].'" ';
        }
        $query = 'SELECT cacheID FROM tags_has_caches WHERE '.$tags_has_cachesWhere.'GROUP BY cacheID';
        $result = $this->base->query($query);
        $rows = null;
        $rows = $result->fetchAll();
        if($rows){
          $cacheWhere = '';
          foreach($rows as $index => $row){
            if($index){
              $cacheWhere .='or ';
            }
            $cacheWhere .='ID = "'.$row['cacheID'].'" ';
          }
          $query = 'SELECT hash_key FROM caches WHERE '.$cacheWhere;
          $result = $this->base->query($query);
          $rows = null;
          $rows = $result->fetchAll();

          foreach($rows as $row){
            $this->cache->cacheSpecificClearItem($row['hash_key']);
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
        $result = $this->base->query($query);
        $rowsa = $result->fetchAll();

        $query = 'SELECT * FROM caches';
        $result = $this->base->query($query);
        $rowsb = $result->fetchAll();

        $query = 'SELECT * FROM tags_has_caches';
        $result = $this->base->query($query);
        $rowsc = $result->fetchAll();
  }
}
