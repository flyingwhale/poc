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
/**
 * This class is a really hady feature in some cases, if you cannot define
 * some states of your application that shall not be cached by the black list
 * feature of the Filter class. You have a chance to define your "blacklist"
 * conditions by analizing the output.
 * 
 * @author Imre Toth
 *
 */

namespace POC\cache\filtering;

class OutputFilter{
  
  private $outputBlacklist = null;
  
  public function addBlacklistCondition($condition){
    $this->outputBlacklist[] = $condition;
  }

  public function isOutputBlacklisted ($output){
    if( $this->outputBlacklist ){
      foreach( $this->outputBlacklist as $condititon ){
        $result = preg_match($condititon, $output);
        if($result){
          return true;
        }
      }
    }
    return false;
  }
  
}