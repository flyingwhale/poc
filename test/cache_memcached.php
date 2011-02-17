<?php

  include ('../framework/src/autoload.php');
  
   //$flexEval = new FlexEvaluateable('/dev/pob/test/cache_apc.php','$_SERVER["REQUEST_URI"]');
   $flexEval = new FlexEvaluateable('|php$|','$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH);

  $pob  = new Pob(new PobCache(new MemcachedCache($flexEval,5,'localhost')));

  include('lib/text_generator.php');

