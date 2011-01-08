<?php
class FileCache extends PobCacheAbstract {

  const KEY_PREFIX = 'POB_CACHE#';
  const TTL_PREFIX = 'POB_CACHE_TTL#';
  
  var $directory;
  var $file;
  var $fileTtl;

  
  function __construct(Evaluatable $evaluatable, $directory) {
    parent::__construct($evaluatable);
    $this->directory = $directory;
    $this->file = $directory.self::KEY_PREFIX.$this->key;
    $this->fileTtl = $directory.'self::TTL_PREFIX'.$this->key;
  }
  
  public function cacheSpecificFetch() {
    if($this->checkTtl()) {
      $handle = fopen($this->file, "r");
      return fread($handle, filesize($this->file));
    }
  }

  public function cacheSpecificClear() {
    if($this->cacheSpecificCheck()) {
      unlink($this->file);
    }
  }

  public function cacheSpecificStore($output, $ttl) {
    $fp = fopen($this->file, 'w');
    fwrite($fp, $output);
    fclose($fp);

    $this->writeTtl($ttl);
    
  }

  
  public function writeTtl($ttl){
    $fp = fopen($this->fileTtl, 'w');
    fwrite($fp, time()+$ttl);
    fclose($fp);
  }

  public function checkTtl(){
    if(file_exists($this->fileTtl)){
      $handle = fopen($this->fileTtl, "r");
      $ttl=fread($handle, filesize($this->file));
      if((int) $ttl>=time()){
        return true;
      } else {
        unlink($this->file);
        unlink($this->fileTtl);
      }
    }
    else return false;
  }
  

}
