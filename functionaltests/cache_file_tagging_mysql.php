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

use POC\Poc;
use POC\cache\cacheimplementation\FileCache;
use POC\cache\cacheimplementation\CacheParams;
use POC\cache\tagging\MysqlTagging;
  
  
include ("../framework/autoload.php");

$cache = new FileCache(array(FileCache::PARAM_TAGDB => new MysqlTagging()));

if(isset($_GET)){
  if(isset($_GET['delcache'])){
    if($_GET['delcache']){
        $cache->addCacheInvalidationTags(true,'user');
    }
  }
}
$cache->addCacheAddTags(true,"user,customer");
$poc  = new Poc(array(Poc::PARAM_CACHE => new FileCache(), Poc::PARAM_DEBUG => true));
$poc->start();
include('lib/text_generator.php');
