<?php
class SqliteTagging extends AbstractDb {

  private $dbFile;
  private $tagIDs = array();
  private $base;
  private $logger;

  function __construct($file="/tmp/POB01") {
    $this->dbFile = $file;
    $this->logger = new Logger('/tmp/logs/logSqliteTagging');
    parent::__construct();
  }

  function checkDb(){
    $ret = file_exists($this->dbFile);
    $this->openDb();
    return $ret;
  }

  private function openDb(){
    $this->base = new SQLiteDatabase($this->dbFile, 0666, $err);
    if ($err) {
      echo($err.' sdaf asdf sdf sdf sdaf ');
    }
//    $this->createDb();
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
//        $this->logger->lwrite('addtags1');
        $this->base->queryexec('INSERT INTO tags VALUES (null, "'.$tag.'")');
        $tagIds[$tag] = $this->base->lastInsertRowID();
      }
      else{
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
      $query = 'SELECT * FROM tags_has_caches where tagID = "'.$tagId.'" AND cacheID="'.$rowCache['ID'].'"';
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
            $this->logger->lwrite('deletting cache: '.$row['hash_key']);
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
    //$this->logger->lwrite('flushoutdated()');
  }

  function dbinfo(){
        $query = 'SELECT * FROM tags';
        $this->logger->lwrite($query);
        $result = $this->base->query($query);
        $rowsa = $result->fetchAll();
        $this->logger->lwrite('Tags: '.serialize($rowsa));

        $query = 'SELECT * FROM caches';
        $this->logger->lwrite($query);
        $result = $this->base->query($query);
        $rowsb = $result->fetchAll();
        $this->logger->lwrite('Caches: '.serialize($rowsb));

        $query = 'SELECT * FROM tags_has_caches';
        $this->logger->lwrite($query);
        $result = $this->base->query($query);
        $rowsc = $result->fetchAll();
        $this->logger->lwrite('Tags has Caches: '.serialize($rowsc));
  }
}