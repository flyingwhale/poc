<?php
class SqliteTagging extends AbstractDb {

  private $dbFile;

  static private $tagIDs = array();

  private $base;

  function __construct($file="/tmp/POB01") {
    $this->dbFile = $file;
    parent::__construct();
//  $this->base = new SQLiteDatabase($this->dbFile, 0666, $err);
//  if ($err) {
//    die($err);
//    echo($err.' sdaf asdf sdf sdf sdaf ');
//  }
  }

  function checkDb(){
    $status = file_exists($this->dbFile);
    $this->openDb();
    return $status;
  }

  private function openDb(){
    $this->base = new SQLiteDatabase($this->dbFile, 0666, $err);
    if ($err) {
      echo($err.' sdaf asdf sdf sdf sdaf ');
    }
    return true;
  }

  function createDb(){
    $query = 'CREATE TABLE tags(
              ID INTEGER PRIMARY KEY,
              tag CHARACTER(32))';
    $results = $this->base->queryexec($query);

    $query = 'CREATE TABLE tags_has_conditions(
              INTEGER tagID,
              INTEGER conditionsID';
    $results = $this->base->queryexec($query);

    $query = 'CREATE TABLE conditions(
              ID INTERGER PRIMARY KEY,
              hash CHARACTER(32),
              valid_to DATETIME
              )';
    $results = $this->base->queryexec($query);
  }

  protected function addTags($tags) {
    $tagArray = explode(',',$tags);
    $tagId;
    foreach($tagArray as $tag){
      $query = 'SELECT ID FROM tags where tag = "'.$tag.'"';
      $result = $this->base->query($query);
      $row = $result->fetch();
      if(!$row){
        $this->base->query('INSERT INTO tags VALUES(null, "'.$tag.'")');
        $tagId[$tag] = $this->base->lastInsertRowID();
      }
      else{
        $tagId[$tag] = $row['ID'];
      }
    }
    return($tagId);
  }


  function addCacheToTags($tags) {
    return $this->addTags($tags);
  }

  function TagInvalidate() {
  }

  function flushOutdated() {
  }

}