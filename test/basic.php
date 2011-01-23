<?php
  
  include ("../framework/src/autoload.php");
  
//  $flexRes = new FlexResource('|^/dev/pob/test/basic.php$|', '$_SERVER["REQUEST_URI"]', Resource::PREGMATCH);
  $flexRes = new FlexEvaluateable('|php$|','$_SERVER["REQUEST_URI"]', Evaluateable::PREGMATCH);
  $flexRes
  ->_or(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::PREGMATCH))
  //->_or(new FlexResource('|^php$|','$_SERVER["REQUEST_URI"]', Resource::PREGMATCH))
  ;
  
  $pob  = new Pob(new PobCache(new ApcCache($flexRes,5)));

  include('text.php');
