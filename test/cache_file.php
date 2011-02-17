<?php

  include ("../framework/src/autoload.php");

  $flexEval = new FlexEvaluateable
       ('#^/dev/pob/test/#','$_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH);
  $pob  = new Pob(new PobCache(new FileCache($flexEval,5,'/tmp/')));

  include('lib/text_generator.php');
