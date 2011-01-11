<?php
  apc_clear_cache();
  include ('../framework/src/autoload.php');
  
  $flexRes = new FlexResource('/dev/pob/test/basic3.php','$_SERVER["REQUEST_URI"]');

  $pob  = new Pob(new PobCache(new MemcachedCache($flexRes,5,'localhost')));

  include('text.php');
