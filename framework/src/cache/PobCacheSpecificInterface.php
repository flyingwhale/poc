<?php

interface PobCacheSpecificInterface {
  
  public function cacheSpecificFetch($key);

  public function cacheSpecificClear($key);
  
  public function cacheSpecificStore($key, $output);
  
  
}

