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

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
/*  
require_once 'Rediska.php';  
  
      $options = array(
      'namespace' => 'Application_',
      'servers'   => array(
        array('host' => '127.0.0.1', 'port' => 6379)
      )
    );
    
    $className = 'Rediska';
    
    if(!class_exists($className)) {
      throw new Exception(sprintf("%s class not exists", $className));      
    }
    
    $rediska = new $className($options);
  
    $key= "k1";
    
    
//    $rediska->flushdb();
    $keyobj1 = new Rediska_Key($key);
    $value = $keyobj1->getValue();
    print $value;
    exit;
*/  
  use POC\cache\filtering\Evaluateable;
  use POC\Poc;
  include ('../framework/autoload.php');
  
  $eval = new Evaluateable('#php$#',$_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH);

  $red = new RediskaCache($eval, 5, 'localhost');
  $pob  = new Poc(new \POC\cache\PocCache(new RediskaCache($eval, 5, 'localhost')), new \POC\handlers\ServerOutput(), true);

  include('lib/text_generator.php');
?>
