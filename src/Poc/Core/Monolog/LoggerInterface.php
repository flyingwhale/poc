<?php
namespace Poc\Core\Monolog;

interface LoggerInterface
{

    public function getLogger ($eventName);

    public function setLog ($eventName, $output);
}
