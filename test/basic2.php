<?php
  apc_clear_cache();
  include ("../framework/src/autoload.php");

  $flexRes = new FlexResource('/dev/pob/test/basic2.php','$_SERVER["REQUEST_URI"]');
  $pob  = new Pob(new PobCache(new FileCache($flexRes,5,'/tmp/')));

  include('text.php');
?>