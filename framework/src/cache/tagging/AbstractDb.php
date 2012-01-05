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

namespace POC\cache\tagging;

abstract class AbstractDb {

  var $cache;

  function __construct()
  {
    $this->flushOutdated();    
  }

  function addCache($cache) {
    $this->cache = $cache;
  }

  protected abstract function initDbStructure();
  protected abstract function createDb();
  protected abstract function createTables();
  public abstract function addCacheToTags($tags,$key, $expires);
  public abstract function TagInvalidate($tags);
  public abstract function flushOutdated();
}
