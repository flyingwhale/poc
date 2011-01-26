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
  || $class_name == 'PobCache' 
  || $class_name == 'PobCacheSpecificInterface' 
  || $class_name == 'FileCache'|| $class_name == 'MemcachedCache'
  || $class_name == 'AbstractPobCacheSpecific'

  ) {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.$class_name.'.php');
    return 1;
  }
  
  // ./cache/filtering/ directory 
  if($class_name == 'HasValue' || $class_name == 'ToString') {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.'filtering'
      .DIRECTORY_SEPARATOR.$class_name.'.php');
    return 1;
  }
   
  // ./cache/filtering/conditions directory 
  if($class_name == 'Evaluateable' 
                     || $class_name == 'HasPattern' || $class_name == 'HasValue'
            || $class_name == 'FlexEvaluateable' || $class_name == 'FlexPattern'
                                            || $class_name == 'SelfEvaluateable'
                ) {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.'filtering'
      .DIRECTORY_SEPARATOR.'conditions'.DIRECTORY_SEPARATOR.$class_name.'.php');
    return 1;
  }
  
  // ./cache/filtering/variables directory 
  if($class_name == 'FlexVariable') {
    include_once($base.'cache'.DIRECTORY_SEPARATOR.'filtering'
      .DIRECTORY_SEPARATOR.'variables'.DIRECTORY_SEPARATOR.$class_name.'.php');
    return 1;
  }
  
}