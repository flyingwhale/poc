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

namespace POC\core;

class Optioner {

  /**
   * 
   * @param OptionAble $oa
   */
  public function __construct($oa){
  	$oa->fillDefaults();
    $implementedinterfaces = (class_implements(get_class($oa)));

    if(isset($implementedinterfaces['POC\core\OptionAbleInterface'])){

      //if(is_array($oa)){
      if(1){
        $options = $this->optionsMerge($oa->getOptions(), $oa);
      } else {
        throw new \Exception('Please add an array or nothing to the
                                                               $options parameter');
      }

      $oa->setOptions($options);
     } else {
       throw new \Exception("Please Pass to the Optioner an instance of the
       OptionAbleInterface");
     }

   }

  /**
   * 
   * @param array $srcArray
   * @param OptionAble $oa
   * @return array
   */
  public function optionsMerge($srcArray, $oa){
    foreach($oa->getIndexes() as $key => $value){
      //var_dump($oa->getIndexes());die();
      if(!isset($srcArray[$value])) {
        $srcArray[$value] = $oa[$value];
      }
    }
    return $srcArray;
  }
}
