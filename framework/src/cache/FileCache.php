<?php
class FileCache extends PobCacheAbstract {

  var $directory;
  var $file;
  var $fileTtl;
  
  function __construct(Evaluatable $evaluatable, $directory){
    parent::__construct($evaluatable);
    $this->directory = $directory;
    $this->file = $directory.'POB_CACHE#'.$this->key;
    $this->fileTtl = $directory.'POB_CACHE_TTL#'.$this->key;
  }
  
  public function cacheSpecificFetch() {
    if($this->checkTtl()) {
      if($this->cacheSpecificCheck()) {
        $handle = fopen($this->file, "r");
        return fread($handle, filesize($this->file));
      }
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

  public function cacheSpecificCheck() {
    return file_exists($this->file);
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
