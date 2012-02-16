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
/**
 * This Interface is Used by the OptionAble class. Any class that implements
 * this class can be passed to the that.
 * 
 * @author Imre Toth
 *
 */
namespace Poc\Core\OptionAble;

interface OptionAbleInterface
{
  /**
   * This class will define the default values in an OptionAble object.
   */
  public function fillDefaults();
}
