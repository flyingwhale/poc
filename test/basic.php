<?php
  apc_clear_cache();
  include ("../framework/src/autoload.php");

  $url  = new Url('/dev/pob/test/basic.php');

  $pob  = new Pob(new PobCache(new ApcCache($url,5)));

  include('text.php');
