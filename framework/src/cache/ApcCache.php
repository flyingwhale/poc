<?php
//bool apc_add ( string $key , mixed $var [, int $ttl = 0 ] )
class ApcCache extends PobCacheAbstract {

  public function storeCache ( $output, $ttl) {
    
    if ($this->evaluatable->evaluate()) {
       apc_add ($this->generateKey($this->evaluatable), $output, $ttl);
    }
  }
  
  public function fetchCache () {
    if($this->evaluatable->evaluate()){
      return apc_fetch($this->generateKey($this->evaluatable));
    }
  }
  
  public function clearCache () {
    if($this->evaluatable->evaluate()){
    }
  }

  public function generateKey () {
    $key=var_export($this->evaluatable,true);
    return md5($key);
  }
  
  public function checkKey () {
      return(apc_exist($this->generateKey($this->evaluatable)));
  }

}
