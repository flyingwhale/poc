<?php

function __autoload($class_name){
  
	//$base = '/home/it/dev/pob/framework/src'.DIRECTORY_SEPARATOR;
	$base = '';
  	
  // ./  directory
  if($class_name == 'Pob'){
    include_once($base.$class_name.'.php');
  }
  
  // ./cache directory
  if($class_name == 'PobCacheInterface' || $class_name == 'ApcCache'
  || $class_name == 'PobCacheAbstract' 
  || $class_name == 'PobCacheSpecificInterface' 
  || $class_name == 'FileCache') {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.$class_name.'.php');
  }
  
  // ./cache/conditions directory 
  if($class_name == 'Evaluatable'|| $class_name == 'Condition' 
    || $class_name == 'Resource' || $class_name == 'Url') {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.'conditions'.DIRECTORY_SEPARATOR.$class_name.'.php');
  }
  
}