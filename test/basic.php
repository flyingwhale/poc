<?php
  
  include ("../framework/src/autoload.php");

  $flexRes = new FlexResource('/dev/pob/test/basic.php','$_SERVER["REQUEST_URI"]');

  $pob  = new Pob(new PobCache(new ApcCache($flexRes,5,Resource::CONTAINS)));

  include('text.php');
