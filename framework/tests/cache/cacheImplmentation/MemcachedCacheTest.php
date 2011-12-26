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

namespace unittest;
use POC\cache\cacheimplementation\MemcachedCache;
use POC\cache\tagging\MysqlTagging;

//include_once '../../../autoload.php';

class MemcachedCacheTest extends CacheTest
{

  function setUp_() {
    $this->cache = new MemcachedCache($this->hasher, parent::TTL, new MysqlTagging());
  }

}

