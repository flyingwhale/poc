<?php
class FileCache extends AbstractPobCacheSpecific {

  const KEY_PREFIX = 'POB_CACHE#';
  const TTL_PREFIX = 'POB_CACHE_TTL#';
  
  var $directory;
  var $file;
  var $fileTtl;

  
  function __construct(Evaluateable $evaluatable, $ttl, $directory) {
    $this->directory = $directory;
    $this->file = $directory.self::KEY_PREFIX;
    $this->fileTtl = $directory.self::TTL_PREFIX;
    $this->ttl = $ttl;
    $this->evaluatable = $evaluatable;
  }
  
  public function cacheSpecificFetch($key) {
    if($this->checkTtl($key)) {
      $handle = fopen($this->file.$key, "r");
      return fread($handle, filesize($this->file.$key));
    }
  }

  public function cacheSpecificClear($key) {
    if($this->cacheSpecificCheck()) {
      unlink($this->file.$key);
      unlink($this->fileTtl.$key);
    }
  }

  public function cacheSpecificStore($key,$output) {
    $fp = fopen($this->file.$key, 'w');
    fwrite($fp, $output);
    fclose($fp);
    $this->writeTtl($this->ttl,$key);
  }

  
  public function writeTtl($ttl,$key){
    $fp = fopen($this->fileTtl.$key, 'w');
    fwrite($fp, time()+$ttl);
    fclose($fp);
  }

  public function checkTtl($key){
    if(file_exists($this->fileTtl.$key)){
      $handle = fopen($this->fileTtl.$key, "r");
      $ttl=fread($handle, filesize($this->file.$key));
      if((int) $ttl>=time()){
        return true;
      } else {
        unlink($this->file.$key);
        unlink($this->fileTtl.$key);
      }
    }
    else return false;
  }
}
