<?php

interface PobCacheSpecificInterface {
  
  public function cacheSpecificFetch();

  public function cacheSpecificClear();
  
  public function cacheSpecificStore( $output, $ttl);
  
  public function cacheSpecificCheck();
  
}

