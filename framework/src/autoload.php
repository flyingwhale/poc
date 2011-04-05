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
function __autoload($class_name){

  // ./  directory
  if($class_name == 'Pob'){
    include_once($class_name.'.php');
  }

  // ./cache directory
  elseif($class_name == 'PobCacheInterface' ||
         $class_name == 'PobCache') {
    include_once('cache'.DIRECTORY_SEPARATOR.$class_name.'.php');
  }

  // ./cache/cacheImplementation directory
  elseif($class_name == 'FileCache'||
         $class_name == 'MemcachedCache' ||
         $class_name == 'AbstractPobCacheSpecific' ||
         $class_name == 'PobCacheSpecificInterface'||
         $class_name == 'ApcCache' ) {
    include_once('cache'.DIRECTORY_SEPARATOR.'cacheImplementation'.DIRECTORY_SEPARATOR.$class_name.'.php');
  }

  // ./cache/filtering/ directory 
  elseif($class_name == 'HasValue' ||
         $class_name == 'ToString' ||
         $class_name == 'ToHash' ||
         $class_name == 'Evaluateable') {
    include_once('cache'.DIRECTORY_SEPARATOR.'filtering'
      .DIRECTORY_SEPARATOR.$class_name.'.php');
  }

  // ./cache/tagging/ directory 
  elseif($class_name == 'AbstractDb' ||
     $class_name == 'SqliteTagging') {
    include_once('cache'.DIRECTORY_SEPARATOR.'tagging'.DIRECTORY_SEPARATOR.$class_name.'.php');
  }
return 1;
}