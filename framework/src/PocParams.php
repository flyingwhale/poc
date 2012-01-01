<?php
namespace POC;

use framework\src\cache\cacheimplementation\CacheParams;

interface PocParams extends CacheParams{
  const PARAM_CACHE = 'cache';
  const PARAM_OUTPUTHANDLER = 'outputHandler';
  const PARAM_HEADERMANIPULATOR = 'headerManipulator';
  const PARAM_OUTPUTFILTER = 'outputFilter';
  const PARAM_DEBUG = 'debug';
}

?>