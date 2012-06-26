<?php
/*
 * Copyright 2011 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */
/**
 * This is tha main class this of the system this is the conductor of the
 * system every functionalitys root if we inspect the flow of the application.
 *
 * @author Imre Toth
 *
 */
namespace Poc;

use Poc\Core\Monolog\MonoLogger;
use Poc\PocEvents\PocEventNames;
use Poc\Events\BaseEvent;
use Poc\Core\Event\PocDispatcher;
use Poc\Core\Event\EventDictionary;
use Poc\Handlers\Callback\CallbackHandler;
use Poc\Handlers\Output\ServerOutput;
use Poc\Handlers\Output\OutputInterface;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Core\PluginSystem\Plugin;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Optionable;
/**
 * This class contains the "Entry point" of the caching process.
 * Therefor is is
 * a really crucial part of the framework. This class implements the
 * PocParams interface.
 *
 *
 * @package Poc
 * @author Imre Toth
 *
 */
class Poc implements PocParams
{
    /**
     *
     * @var OutputInterface
     */
    private $outputHandler = null;

    /**
     *
     * @var String
     */
    private $output = null;

    /**
     * If its value is true teh debug mod is turned on.
     *
     * @var boolean
     */
    private $debug = null;

    /**
     * When the start function of the class executed sets its value by
     * calling the microtime function.
     *
     * @var unknown_type
     */
    private $startTime = null;

    /**
     *
     * @var unknown_type
     */
    private $level = null;

    /**
     *
     * @var \POC\cache\header\HeaderManipulator
     */
    private $headerManipulator = null;

    /**
     *
     * @var OutputFilter
     */
    private $outputFilter = null;

    /**
     *
     * @var Cache
     */
    private $cache = null;

    /**
     *
     * @var Optionable
     *
     */
    private $optionable;

    /**
     *
     * @var EventDictionary
     */
    private $eventDictionary;

    /**
     *
     * @var EventDispatcher;
     */
    private $pocDispatcher;

    /**
     *
     * @var MonoLogger;
     */
    private $logger;

    private $canICacheThisGeneratedContent = true;

    /**
     *
     * @var Cache\Filtering\Hasher
     */
    private $hasher;

    /**
     *
     * @var Cache\Filtering\Filter
     */
    private $filter;

    /**
     *
     * @var CallbackHandler
     */
    private $callbackHandler;
    /**
     *
     * @param Core\PluginSystem\Plugin $plugin
     */
    public function addPlugin ($plugin)
    {
        $plugin->init($this);
    }

    /**
     *
     * @return the $pocDispatcher
     */
    public function getPocDispatcher ()
    {
        return $this->pocDispatcher;
    }

    /**
     *
     * @return the $startTime
     */
    public function getStartTime ()
    {
        return $this->startTime;
    }

    public function setDebug ($debug)
    {
        $this->debug = $debug;
    }

    public function getDebug ()
    {
        return $this->debug;
    }

        /**
     *
     * @return the $output
     */
    public function getOutput ()
    {
        return $this->output;
    }

    /**
     *
     * @param $output string
     */
    public function setOutput ($output)
    {
        $this->output = $output;
    }

    public function destruct ()
    {
        $this->__destruct();
    }

    public function getLogger ()
    {
        if (! $this->logger) {
            $this->logger = new MonoLogger();
        }

        return $this->logger;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getOutputHandler()
    {
        return $this->outputHandler;
    }

    public function setCanICacheThisGeneratedContent($bool)
    {
        $this->canICacheThisGeneratedContent = $bool;
    }

    public function getHasher()
    {
        return $this->hasher;
    }

    public function getLevel(){
        return $this->level;
    }

    public function getCanICacheThisGeneratedContent(){
        return $this->canICacheThisGeneratedContent;
    }

    public function getHeaderManipulator(){
        return $this->headerManipulator;
    }

    public function getCallbackHandler(){
        return $this->callbackHandler;
    }

    protected function setupDefaults (&$optionable)
    {
        $optionable->setDefaultOption(Poc::PARAM_CACHE,
            function  () {
                return new FileCache();
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_OUTPUTHANDLER,
            function  () {
                return new ServerOutput();
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_HEADERMANIPULATOR,
            function  () {
                return new HeaderManipulator();
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_OUTPUTFILTER,
            function  () {
                return null;
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_DEBUG,
            function  () {
                return false;
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_CIA_PROTECTOR,
            function  () {
                return null;
            }
        );
        $optionable->setDefaultOption(Poc::PARAM_EVENT_DISPATCHER,
            function  () {
                return new EventDispatcher();
            }
        );
        $optionable->setDefaultOption(Poc::PARAM_HASHER,
            function  () {
                return new Hasher();
            }
        );
        $optionable->setDefaultOption(Poc::PARAM_FILTER,
            function  () {
                return new Filter();
            }
        );

    }


    protected function mapFieldsFromOptionable(&$optionable, &$poc)
    {
        $poc->pocDispatcher =  $optionable[Poc::PARAM_EVENT_DISPATCHER];
        $poc->cache = $optionable[Poc::PARAM_CACHE];
        $poc->outputHandler = $optionable[Poc::PARAM_OUTPUTHANDLER];
        $poc->outputHandler->setPoc($this);
        $poc->headerManipulator = $optionable[Poc::PARAM_HEADERMANIPULATOR];
        $poc->headerManipulator->setOutputHandler($this->outputHandler);
        $poc->headerManipulator->setPoc($this);
        $poc->outputFilter = $optionable[Poc::PARAM_OUTPUTFILTER];
        $poc->setDebug($optionable['debug']);
        $poc->filter = $optionable[Poc::PARAM_FILTER];
        $poc->hasher = $optionable[Poc::PARAM_HASHER];
        $this->callbackHandler = new CallbackHandler($this);
    }

    /**
     *
     * @param $cache PocCacheInterface
     *            this placeholder class contains the various
     *            caches.
     * @param $debug bool
     *            If true debug messages are provided in the output, only
     *            for develompment purposevags.
     */
    public function __construct ($options = array())
    {
        $this->startTime = microtime();
        $this->optionable = new Optionable($options);
        $this->setupDefaults($this->optionable);
        $this->mapFieldsFromOptionable($this->optionable, $this);
        $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END, new BaseEvent($this));
    }

    public function fetchCache ($die = true)
    {
        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINING, new BaseEvent($this));

        $output = $this->fetchCacheValue();
        if ($output) {
            $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_CACHE);
            $this->headerManipulator->fetchHeaders();
            // TODO:Replace it to it's appropriate place.(OutputHandler)
            $arr = headers_list();
            if ($this->headerManipulator->headersToSend) {
                foreach ($this->headerManipulator->headersToSend as $header) {
                    $this->outputHandler->header($header);
                }
                $this->headerManipulator->removeHeaders($arr);
            }
            $this->outputHandler->stopBuffer($output);
        }

        return $output;
    }

    public function fetchCacheValue ()
    {
        $output = $this->cache->fetch($this->hasher->getKey());

        return $output;
    }

    public function start ()
    {

        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINING,
        new BaseEvent($this));

        $this->level = \ob_get_level();
        if ($this->filter->evaluate()) {
            if (! $this->fetchCache()) {
                $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_GENERATE);

                $this->pocDispatcher->dispatch(PocEventNames::FUNCTION_START_ENDS_CACHE_STARTS,
                        new BaseEvent($this));
            }
        } else {

            $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_SHOWOUTPUT);

            $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END,
                    new BaseEvent($this));
        }
    }

    public function __destruct ()
    {
        if (isset($this->level)) {
            if ($this->level) {
                $this->outputHandler->obEnd();
            }
        }
        /*$this->pocDispatcher->dispatch(PocEventNames::DIES,
                new BaseEvent($this));*/
    }

}
