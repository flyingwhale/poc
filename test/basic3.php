<?php
  apc_clear_cache();
  include ('../framework/src/autoload.php');

  $url  = new Url('/dev/pob/test/basic3.php');

  $pob  = new Pob(new PobCache(new MemcachedCache($url,5,'localhost')));

  include('text.php');
