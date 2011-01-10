<?php
  
  include ("../framework/src/autoload.php");

  $url  = new Url('/dev/pob/test/basic.php');

  $pob  = new Pob(new PobCache(new ApcCache($url,22)));

  include('text.php');
