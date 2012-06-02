<?php
namespace Poc\PocPlugins\Logging;

use Poc\Events\BaseEvent;

use Poc\PocEvents\PocEventNames;

use Monolog\Logger;

use Poc\Core\Event\PocDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Poc\Poc;

use Poc\Core\PluginSystem\Plugin;

class PocLogs extends Plugin
{

    const LOG_TYPE_OUTPUT = "OUTPUT";

    const LOG_TYPE_TIME = "TIME";

    const SIZE_OF_OUTPUT_CHUNK = 25;

    const OUTPUT_CHUNK_DELIMITER = '. ... .';

    /**
     *
     * @var EventDispatcher
     */
    private $pocDispatcher;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    public function init (Poc $poc)
    {

        $this->poc = $poc;
        $this->logger = $this->poc->getLogger();

        $this->pocDispatcher = $this->poc->getPocDispatcher();

        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                array($this, 'beforeOutputSentToClinetAfterOutputStoredTime'));
        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                array($this, 'beforeOutputSentToClinetAfterOutputStoredOutput'));

        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,
                array($this, 'beforeOutputSentToClientNoCachingProcessInvolvedTime'));
        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,
                array($this, 'beforeOutputSentToClientNoCachingProcessInvolvedOutput'));

        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                array($this, 'beforeOutputSentToClientFetchedFromCacheTime'));
        $this->pocDispatcher->addListener(
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                array($this, 'beforeOutputSentToClientFetchedFromCacheOutput'));

        $this->pocDispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT,
                array($this, 'beforeStoreOutputTime'));
        $this->pocDispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT,
                array($this, 'beforeStoreOutputOutput'));

        // todo: If it is turned on, the php fly away with segmentation fault
        // when phpunit runs.
        /*
         * $this->pocDispatcher->addListener(PocEventNames::DIES, array($this,
         * 'diesTime'));
         */
    }

    public function beforeOutputSentToClinetAfterOutputStoredTime (BaseEvent $event)
    {
        $this->logTime($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                self::LOG_TYPE_TIME);
    }

    public function beforeOutputSentToClinetAfterOutputStoredOutput (BaseEvent $event)
    {
        $this->logOutput($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED,
                self::LOG_TYPE_OUTPUT);
    }

    public function beforeOutputSentToClientNoCachingProcessInvolvedTime (
            BaseEvent $event)
    {
        $this->logTime($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,
                self::LOG_TYPE_TIME);
    }

    public function beforeOutputSentToClientNoCachingProcessInvolvedOutput (
            BaseEvent $event)
    {
        $this->logOutput($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED,
                self::LOG_TYPE_OUTPUT);
    }

    public function beforeOutputSentToClientFetchedFromCacheTime (BaseEvent $event)
    {
        $this->logTime($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                self::LOG_TYPE_TIME);
    }

    public function beforeOutputSentToClientFetchedFromCacheOutput (BaseEvent $event)
    {
        $this->logOutput($event,
                PocEventNames::BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE,
                self::LOG_TYPE_OUTPUT);
    }

    public function beforeStoreOutputTime (BaseEvent $event)
    {
        $this->logTime($event, PocEventNames::BEFORE_STORE_OUTPUT,
                self::LOG_TYPE_TIME);
    }

    public function beforeStoreOutputOutput (BaseEvent $event)
    {
        $this->logOutput($event, PocEventNames::BEFORE_STORE_OUTPUT,
                self::LOG_TYPE_OUTPUT);
    }

    public function diesTime ($event)
    {
        $this->logTime($event, PocEventNames::DIES, self::LOG_TYPE_TIME);
        $this->pocDispatcher->removeListener(PocEventNames::DIES,
                array($this, 'beforeStoreOutputOutput'));
    }

    private function logOutput (BaseEvent $event, $eventName, $type)
    {
        $this->logOutputMatix($eventName,
                $event->getEvent()
                    ->getOutput(), $type);
    }

    private function logTime (BaseEvent $event, $eventName, $type)
    {
        $this->logOutputMatix($eventName,
               \microtime() - $event->getEvent()
                    ->getStartTime() . '|' . $eventName, $type);
    }

    private function logOutputMatix ($eventName, $saveIt, $type)
    {
        $this->logger->setLog($type, $saveIt);

        if ($type == self::LOG_TYPE_OUTPUT) {
            if ($saveIt) {
                $size = strlen($saveIt);

                if ($size > (self::SIZE_OF_OUTPUT_CHUNK * 2) + self::OUTPUT_CHUNK_DELIMITER) {
                    $output = substr($saveIt, 0, self::SIZE_OF_OUTPUT_CHUNK) . self::OUTPUT_CHUNK_DELIMITER . substr(
                            $saveIt,
                            \strlen($saveIt) - self::SIZE_OF_OUTPUT_CHUNK,
                            self::SIZE_OF_OUTPUT_CHUNK);
                } else {
                    $output = $saveIt;
                }
                $output .= '... |the output size is ' . $size . ' bytes';
            } else {
                // this case is currently is not stored by the poc
                $output = 'There was no output';
            }
        } else {
            $output = $saveIt;
        }
        $this->logger->setLog($eventName, $output);
        $this->logger->setLog($type . '-' . $eventName, $output);
    }
}

