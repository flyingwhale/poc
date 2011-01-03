<?php
interface PobCacheInterface {
  
  public function storeCache ($output, $ttl);
  
  public function fetchCache ();
  
  public function clearCache ();
  
  public function generateKey ();
  
  public function checkKey ();
  
}
