<?php

namespace Poc\Exception;

class CacheNotReachableException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = 'Cache not reachable';
        return parent::__construct($message, $code);
    }
}