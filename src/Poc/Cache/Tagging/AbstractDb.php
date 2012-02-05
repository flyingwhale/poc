<?php
/*Copyright 2012 Imre Toth <tothimre at gmail>

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

namespace Poc\Cache\Tagging;


abstract class AbstractDb {

  protected $cache;

  public function __construct()
  {
    $this->flushOutdated();    
  }
  
  public function addCache($cache){
    $this->cache = $cache;
  }

  public abstract function addCacheToTags($tags,$key, $expires);
  public abstract function flushOutdated();
  public abstract function tagInvalidate($tags);
  
  protected abstract function createDb();
  protected abstract function createTables();
  protected abstract function initDbStructure();
  
}
