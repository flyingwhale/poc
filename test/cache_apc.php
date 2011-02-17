<?php
  
  include ("../framework/src/autoload.php");
  
//  $flexRes = new FlexResource('|^/dev/pob/test/basic.php$|', '$_SERVER["REQUEST_URI"]', Resource::OP_PREGMATCH);
  $flexEval = new FlexEvaluateable('|php$|','$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH);
  $flexEval
  ->_and(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH));
  $flexEval->_or(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH));
  $flexEval->_or(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH))
  ->_or(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH))
  ->_and(new FlexEvaluateable('|^/dev$|', '$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH))
  //->_or(new FlexResource('|^php$|','$_SERVER["REQUEST_URI"]', Resource::OP_PREGMATCH))
  ;
  $pob  = new Pob(new PobCache(new ApcCache($flexEval,5)));
  
  

  include('lib/text_generator.php');
