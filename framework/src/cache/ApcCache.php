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
*/class ApcCache extends AbstractPobCacheSpecific {

  public function cacheSpecificFetch($key) {
    return apc_fetch($key);
  }

  public function cacheSpecificClearAll() {
    apc_clear_cache('user');
  }

  public function cacheSpecificClearItem($key) {
     apc_delete($key);
  }

  public function cacheSpecificStore($key, $output) {
    apc_add ($key, $output, $this->ttl);
  }

}
