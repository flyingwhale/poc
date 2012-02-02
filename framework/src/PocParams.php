<?php
namespace POC;
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
 * This Interface contains all the possible indexes for the parameters in the
 * Poc object. extends the CachePArams interface.
 *
 * @author Imer Toth
 *
 */
use framework\src\cache\cacheimplementation\CacheParams;

interface PocParams extends CacheParams{
  const PARAM_CACHE = 'cache';
  const PARAM_OUTPUTHANDLER = 'outputHandler';
  const PARAM_HEADERMANIPULATOR = 'headerManipulator';
  const PARAM_OUTPUTFILTER = 'outputFilter';
  const PARAM_DEBUG = 'debug';
  const PARAM_CIA_PROTECTOR = 'cia_protection';
}

?>
