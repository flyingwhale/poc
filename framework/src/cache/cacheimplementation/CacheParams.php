<?php
namespace framework\src\cache\cacheimplementation;

interface CacheParams {
  const PARAM_HASHER = 'hasher';
  const PARAM_FILTER = 'filter';
  const PARAM_TTL = 'ttl';
  const PARAM_TAGDB = 'tagDb';
}

?>