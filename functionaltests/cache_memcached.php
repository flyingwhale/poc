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

  use POC\cache\filtering\Hasher;
  use POC\cache\filtering\Filter;
  use POC\cache\filtering\OutputFilter;
  use POC\Poc;
  use POC\handlers\ServerOutput;
  use POC\cache\PocCache;
  use POC\cache\tagging\MysqlTagging;
  use POC\cache\cacheimplementation\MemcachedCache;
  use POC\cache\header\HeaderManipulator;

  include ('../framework/autoload.php');
  $poc  = new Poc(array(POC::PARAM_CACHE => new MemcachedCache(), POC::PARAM_DEBUG => true));
  $poc->start();
  include('lib/text_generator.php');

