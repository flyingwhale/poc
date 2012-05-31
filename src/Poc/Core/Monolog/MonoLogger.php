<?php
namespace Poc\Core\Monolog;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

class MonoLogger implements LoggerInterface
{

    private $loggers;

    private $token;

    private $logFolder = '/tmp/';

    private $logPrefix = 'POC_LOG_';

    public function __construct ()
    {
        $this->token = md5(time() + rand());
    }

    public function setLog ($eventName, $output)
    {
        $this->getLogger($eventName)->addInfo($output);
    }

    public function getLogger ($eventName)
    {
        if (! isset($this->loggers[$eventName])) {
            $this->loggers[$eventName] = new Logger($this->token);
            $this->loggers[$eventName]->pushHandler(
                    new StreamHandler(
                            $this->logFolder . $this->logPrefix . 'POC_' . $eventName . '.log',
                            Logger::INFO));
        }

        return $this->loggers[$eventName];
    }

}
