<?php
  
  include ("../framework/src/autoload.php");
  
//  $flexRes = new FlexResource('|^/dev/pob/test/basic.php$|', '$_SERVER["REQUEST_URI"]', Resource::PREGMATCH);
  $flexRes = new FlexResource('|php$|','$_SERVER["REQUEST_URI"]', Resource::PREGMATCH);
  $flexRes
  ->_or(new FlexResource('|^/dev$|', '$_SERVER["REQUEST_URI"]', Resource::PREGMATCH))
  //->_or(new FlexResource('|^php$|','$_SERVER["REQUEST_URI"]', Resource::PREGMATCH))
  ;
  
  $pob  = new Pob(new PobCache(new ApcCache($flexRes,5)));

  include('text.php');
