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
namespace POC\cache\cacheimplementation;
use POC\cache\filtering\Evaluateable;
use POC\core\Optioner;
use POC\cache\tagging\Tagger;

class MongoCache extends AbstractPocCacheSpecific
{
  private $isNotConnected;
  private $mongo;

  protected $defaultOptions = array('db_name'=>'poc','collection_name'=>'key_value');

  function __construct($hasher, $ttl,$tagger, $options = array())
  {
    parent::__construct($hasher,$ttl,$tagger);

    $this->isNotConnected = 0;

    $this->options = $options;

    new Optioner($this);

    try
    {
      $className = 'Mongo';

      if(!class_exists($className)) {
        throw new Exception(sprintf("%s class not exists", $className));
      }
      $this->mongo = new $className();
      $this->isNotConnected = 1;
    }
    catch ( MongoConnectionException $e )
    {
      $this->throwDbException();


  }

  public function cacheSpecificFetch($key)
  {
    $value = '';

    $keyValue = $this->findKeyValueObj($key);

    if ($keyValue && $keyValue['expire'] > time())
    {
      $value =  $keyValue['value'];
    }

    return $value;
  }

  public function cacheSpecificClearAll()
  {
    $keyValueCollection = $this->getCollection();
    $keyValueCollection->drop();

  }

  public function cacheSpecificClearItem($key)
  {
    $criteria = array(
			'key'=> $key
    );

    $keyValueCollection = $this->getCollection();
    $keyValueCollection->remove($criteria);

  }

  public function cacheSpecificStore($key, $output)
  {
    $keyValueCollection = $this->getCollection();

    $keyValue = $this->findKeyValueObj($key);

    if (!$keyValue)
    {
      $keyValue = array(
      			'key'    => $key,
      			'value'  => $output,
      			'expire' => time()+$this->ttl
      );
    }
    else {
      $keyValue['value']  = $output;
      $keyValue['expire'] = time()+$this->ttl;
    }

    $keyValueCollection->save($keyValue);

  }

  function  isCacheAvailable()
  {
    return $this->isNotConnected;
  }

  private function getDb()
  {
    $db = $this->mongo->selectDB($this->options['db_name']);

    return $db;
  }

  private function getCollection()
  {
    $collection = $this->getDb()->selectCollection($this->options['collection_name']);

    return $collection;
  }

  private function findKeyValueObj($key)
  {
    $keyValueCollection = $this->getCollection();

    $filter = array(
            'key'=> $key
    );

    $keyValue = $keyValueCollection->findOne($filter);

    return $keyValue;
  }

}
