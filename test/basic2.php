<?php
  apc_clear_cache();
  include ("../framework/src/autoload.php");

  $url  = new Url('/dev/pob/test/basic2.php');
  $pob  = new Pob(new PobCache(new FileCache($url,5,'/tmp/')));

  include('text.php');
?>