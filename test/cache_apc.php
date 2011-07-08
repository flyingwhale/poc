<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
  use POC\cache\filtering\Evaluateable;
  include ("../framework/autoload.php");
//  $flexRes = new FlexResource('|^/dev/pob/test/basic.php$|', '$_SERVER["REQUEST_URI"]', Resource::OP_PREGMATCH);
  $eval = new Evaluateable('#php$#',$_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH);
/*  $flexEval
  ->_and(new FlexEvaluateable('|^/dev$|', $_SERVER["REQUEST_URI"]', Evaluateable::OP_PREGMATCH));
  $flexEval->_or(new FlexEvaluateable('|^/dev$|', $_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH));
  $flexEval->_or(new FlexEvaluateable('|^/dev$|', $_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH))
  ->_or(new FlexEvaluateable('|^/dev$|', $_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH))
  ->_and(new FlexEvaluateable('|^/dev$|', $_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH))
  //->_or(new FlexResource('|^php$|','$_SERVER["REQUEST_URI"]', Resource::OP_PREGMATCH))
  ;*/
  $pob  = new Pob(new PobCache(new ApcCache($eval,5)),true);

  include('lib/text_generator.php');
