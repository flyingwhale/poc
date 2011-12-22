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

use POC\cache\filtering\Evaluateable;
use POC\core\OptionAble;

abstract class AbstractPocCacheSpecific extends OptionAble implements PocCacheSpecificInterface
{
  /** This variable must be declared at the constructors of this class.*/
  var $ttl;

  /** This variable represents the Evaluateable type object belongs to the
  object*/
  var $evaluateable;

  /** The databese that stores the caches*/
  var $tagDb;

  protected $options = array();

  protected $defaultOptions = array();

  function __construct(Evaluateable $eval,$ttl,$tagDb = null) {
     $this->evaluateable = $eval;
     $this->ttl = $ttl;
     $this->evaluateable->setMyCache($this);
  }

  function getEvaluateable(){
    return $this->evaluateable;
  }

  function setTagDb(AbstractDb $tagDb = null){
    if ($tagDb == null) {
      $this->tagDb = new SqliteTagging();
    } else {
      $this->tagDb = $tagDb;
    }
    $this->tagDb->setCache($this);
  }

  function getTagDb(){
    if (!$this->tagDb) {
      $this->setTagDb();
    }
    return $this->tagDb;
  }

  function throwDbException(){
    if(!$this->isCacheAvailable()){
      throw new Exception("The cache database is not reachable!");
    }
  }
}
