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

class ApcCache extends \ApcCache {

  function is_apc_expired($key) {
    $cache = apc_cache_info('user');
    if (empty($cache['cache_list'])) {
//      echo("key1NOTdELETED: ".$key." * ");
      return false;
    }
    foreach ($cache['cache_list'] as $entry) {
      if ($entry['info'] != $key) {
        continue;
      }
      if ($entry['ttl'] == 0) {
        return 0;
      }
      $expire = $entry['creation_time']+$entry['ttl'];
      if($expire < time()){
//        echo("\n keyDELETED: ".$key."\n\n");
        apc_delete($key);
        return true;
      }
    }

//    echo("key2NOTdELETED: ".$key." * ");
    return false;
  }


  public function cacheSpecificFetch($key) {
    if($this->is_apc_expired($key)) {
      return '';
    } else {
      return apc_fetch($key);
    }
  }

}
