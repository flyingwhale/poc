<?php
/*
 * Copyright 2012 Imre Toth <tothimre at gmail> Licensed under the Apache
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
use Poc\Handlers\Callback\CallbackHandler;
use Poc\Handlers\Output\ServerOutput;
use Poc\Handlers\Output\OutputInterface;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Header\HeaderManipulator;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Optionable;

/**
 * This class contains the "Entry point" of the caching process.
 * Therefor is a really crucial part of the framework. The whole process has
 * got bindings at this class. The framework storngly builds on the observer pattern,
 * Tehre fore this is completely plugin based. Every component of the framework can be
 * viewed as an external addition. This makes the the whole development of new modules
 * really easy, all component all decoupled, it also makes the system simple
 * and will use as small amount resources as possible.
 *
 * This aproach also helps the refactoring, and retinking of the basic idea, nameli the
 * really easy and scalable output caching.
 *
 *
 * @package Poc
 * @author Imre Toth
 *
 */
class Poc implements PocParams
{
    /**
     * This object stands for the output handling. I had to make
     * this abstraction because we whant testable code, and for the tests we
     * don't have the server environmnet, and we weeded to mock it somehow.
     * This is the solution for this problem.
     *
     * @var OutputInterface
     */
    private $outputHandler = null;

    /**
     * This variable holds the output that has been created by the output
     * buffering functionality of the PHP. With this captured output the.
     * Plugins we have can make modifications on the output.
     *
     * @var String
     */
    private $output = null;

    /**
     * If its value is true the debug mod is turned on.
     * Tehre are already modules that helps debugging, this variable
     * and the code uses this will be source out to other plugins.
     *
     * @depreceted
     * @var boolean
     */
    private $debug = null;

    /**
     * When the start function of the class executed sets its value by
     * calling the microtime function.
     *
     *
     * @var integer
     */
    private $startTime = null;

    /**
     * This variable sotres the level of the output buffering, when the poc
     * has been turned on.
     *
     * @var unknown_type
     */
    private $level = null;

    /**
     * This class handles the header related manipulations, also takes care
     * about stroing it.
     *
     * @var \POC\cache\header\HeaderManipulator
     */
    private $headerManipulator = null;

    /**
     * This variable contains the object that handles the caching process.
     *
     * @var Cache
     */
    private $cache = null;

    /**
     * This a little extension of the wordfamous micro depenency injection
     * framework pimple that supportts default parameters. This handles the
     * default dependencies of the objects.
     *
     * @var Optionable
     */
    private $optionable;

    /**
     * With the help of this class we implement the observer pattern.;
     *
     * @var EventDispatcher;
     */
    private $pocDispatcher;

    /**
     * This variable takes care about the logging related issues.
     *
     * @var MonoLogger;
     */
    private $logger;

    private $canICacheThisGeneratedContent = true;

    /**
     * This class helps too distinguish between more caches.
     *
     * @var Cache\Filtering\Hasher
     */
    private $hasher;

    /**
     * This helps in balck/white listin the pages that has to be cached or not.
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
        if (!$this->logger) {
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

    /**
     *
     * @return Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getCanICacheThisGeneratedContent()
    {
        return $this->canICacheThisGeneratedContent;
    }

    /**
     *
     * @return Poc\Cache\Header\HeaderManipulator
     */
    public function getHeaderManipulator()
    {
        return $this->headerManipulator;
    }

    public function getCallbackHandler()
    {
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
        $this->startTime = microtime(true);
        $this->optionable = new Optionable($options);
        $this->setupDefaults($this->optionable);
        $this->mapFieldsFromOptionable($this->optionable, $this);
        $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END, new BaseEvent($this));
    }

    public function fetchCache ()
    {
        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINNING, new BaseEvent($this));

        $output = $this->cache->fetch($this->hasher->getKey());
        if ($output) {
            $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_CACHE);
            $this->headerManipulator->fetchHeaders();
            $this->outputHandler->stopBuffer($output);
        } else {
        }
    }

    public function start ()
    {
        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
        new BaseEvent($this));

        $this->level = \ob_get_level();
        if ($this->filter->evaluate()) {
            if (!$this->fetchCache()) {
                $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_GENERATE);
                $this->pocDispatcher->dispatch(
                                PocEventNames::FUNCTION_START_ENDS_CACHE_STARTS,
                                                          new BaseEvent($this));
            }
        } else {
            $this->outputHandler->startBuffer(CallbackHandler::CALLBACK_SHOWOUTPUT);
        }
    }

    public function __destruct ()
    {
        if (isset($this->level)) {
            if ($this->level) {
                $this->outputHandler->obEnd();
            }
        }
//        $this->pocDispatcher->dispatch(PocEventNames::DIES,
//                new BaseEvent($this));
    }

    /**
     *
     * @return Filter
     */
    public function getFilter(){
        return $this->filter;
    }
}
