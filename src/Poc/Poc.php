<?php
/*
 * Copyright 2012 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or im3plied. See the License for the specific language
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

use Poc\Core\PluginSystem\PluginContainer;
use Poc\Core\Monolog\MonoLogger;
use Poc\Core\PocEvents\PocEventNames;
use Poc\Core\Events\BaseEvent;
use Poc\Cache\CacheImplementation\FileCache;
use Poc\Cache\Filtering\Hasher;
use Poc\Cache\Filtering\Filter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Core\PluginSystem\PluginRegistry;
use Optionable;
use Poc\Toolsets\NullOutputHandler\NullCapture;


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
class Poc implements PocParams, PluginContainer
{

    /**
     * This variable holds the output that has been created by the output
     * buffering functionality of the PHP. With this captured output the.
     * Plugins we have can make modifications on the output.
     *
     * @var String
     */
    private $output = null;

    /**
     * When the start function of the class executed sets its value by
     * calling the microtime function.
     *
     *
     * @var integer
     */
    private $startTime = null;

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
     * @var PluginRegistry
     */
    private $pluginRegistry = null;

    /**
     * 
     * @param Core\PluginSystem\PluginInterface $plugin
     */
    public function addPlugin ($plugin)
    {
        $this->pluginRegistry->addPlugin($plugin);
        $plugin->init($this);
    }
    
    /**
     * 
     * @return PluginRegistry
     */
    public function getPluginRegistry()
    {
        return $this->pluginRegistry;
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

    public function end ()
    {
        
        $this->pocDispatcher->dispatch(PocEventNames::END_OF_BUFFERING, 
                                                          new BaseEvent($this));
        //$this->__destruct();
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

    /**
     *
     * @return Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    public function getCanICacheThisGeneratedContent()
    {
        return $this->canICacheThisGeneratedContent;
    }

    protected function setupDefaults (&$optionable)
    {
        $optionable->setDefaultOption(Poc::PARAM_CACHE,
            function  () {
                return new FileCache();
            }
        );

        $optionable->setDefaultOption(Poc::PARAM_TOOLSET,
            function  () {
                // @codeCoverageIgnoreStart
                return new NullCapture();
                // @codeCoverageIgnoreEnd
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
        $poc->cache = $optionable[Poc::PARAM_CACHE];
        $poc->filter = $optionable[Poc::PARAM_FILTER];
        $poc->hasher = $optionable[Poc::PARAM_HASHER];
    }

    public function __construct ($options = array())
    {
        $this->startTime = microtime(true);
        $this->pocDispatcher = new EventDispatcher;
        $this->pluginRegistry = new PluginRegistry();        
        $this->optionable = new Optionable($options);  
        
        $this->setupDefaults($this->optionable);
        
        $this->mapFieldsFromOptionable($this->optionable, $this);
        $this->addPlugin($this->optionable[PocParams::PARAM_TOOLSET]);
        $this->pocDispatcher->dispatch(PocEventNames::CONSTRUCTOR_END, 
                                                          new BaseEvent($this));
    }

    public function fetchCache ()
    {
        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINNING, new BaseEvent($this));

        $this->output = $this->cache->fetch($this->hasher->getKey());
        if ($this->output) {

            $this->pocDispatcher->dispatch(
                    PocEventNames::GET_OUTPUT_FROM_CACHE, new BaseEvent($this));

        } 
    }

    public function start ()
    {
        $this->pocDispatcher->dispatch(
        PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
        new BaseEvent($this));

        if ($this->filter->evaluate()) {
            if (!$this->fetchCache()) {
                $this->pocDispatcher->dispatch(
                                  PocEventNames::CAPTURE, new BaseEvent($this));
            }
        } else {
                $this->pocDispatcher->dispatch(
                                  PocEventNames::MONITOR, new BaseEvent($this));
        }
    }
}
