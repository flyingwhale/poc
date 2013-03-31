<?php

namespace Poc\Exception;

class DriverNotFoundException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = 'Driver not found';
        return parent::__construct($message, $code);
    }
}