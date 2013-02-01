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

namespace Poc\PocPlugins\CacheInvalidationProtection;

use Poc\Poc;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Core\PocEvents\PocEventNames;
use Poc\Core\Events\BaseEvent;
use Optionable;
use Poc\Core\PluginSystem\PluginInterface;
use \Poc\Core\PluginSystem\PluginContainer;
/**
 * This calss name comes form the "RelOad and cache Invalidation Attack Protection" name.
 * This integrates transpanently to the framework.
 *
 * The basic idea was to implement a subsytem to the framework that protects
 * the system that uses against the high load if the caches are invalidated, or
 * just cannot afford long TTL's for the cache, so the pages are generated
 * offten.
 *
 * Alought it can be used in many scenatrios. For instance if you have a page
 * thats generation takes a long time you can use cache with even very sort ttl
 * the cache. If there are lot of concurrent request waits for the page that
 * generates for a long time this system can reduce the load from your server
 * effectively as well by forcing the clients to wait while the first user's
 * output is generated. If it is done the clients in the queue will receive the
 * result. Also we can set up how much client can wait for the results with the
 * sleep php method per page, if there are more requests are coming the clients
 * will be informed about the heavy losad and their client will try to reconnect
 * to the resource within a second again.
 *
 * @author Imre Toth
 *
 */
class ROIProtector implements ROIProtectorParameters, PluginInterface
{

    const LOG_TYPE_CIA = 'ROI';

    public $monoLogger;

    const KEY_POSTFIX = "roi";

    const PARAM_CLIENT_UNIQUE = 'clinetUnique';

    /**
     *
     * @var Optionable
     */
    private $optionable = null;

    /**
     *
     * @var \POC\cache\cacheimplementation\Cache
     */
    private $cache = null;

    private $clientUnique;

    /**
     *
     * @var \Poc\Toolsets\NativeOutputHandlers\Handlers\Output\OutputInterface
     */
    private $outputHandler;

    /**
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    private $maxNumberOfConcurrentConnections;

    /**
     *
     * @param PluginContainer $poc 
     */
    public function init($poc)
    {
        $this->poc = $poc;
        $this->cache = $poc->getCache();
        $this->outputHandler =$poc->getOutputHandler();
        $this->eventDispatcher = $poc->getPocDispatcher();
        $this->monoLogger = $poc->getLogger();

        $poc->getPocDispatcher()->addListener(PocEventNames::OUTPUT_STORED,
                                                 array($this, 'consultFinish'));
        $poc->getPocDispatcher()->addListener(PocEventNames::CAPTURE,
                                                       array($this, 'consult'));

    }

    public function getName() {
        return 'roip';
    }

        public function setupDefaults (&$optionable)
    {
        /*
         * $this->optionable->setDefaultOption('self::PARAM_CLIENT_UNIQUE', function(){ return
         * md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT'].
         * $_SERVER['HTTP_ACCEPT_LANGUAGE'].$_SERVER['HTTP_ACCEPT_ENCODING'].$_SERVER['HTTP_ACCEPT_CHARSET']);
         * });
         */

        $this->optionable[self::PARAM_REDIRECT_CONTENT] =
                '<HTML>
                <HEAD>
                <META HTTP-EQUIV="refresh" content="1; url=' . $this->getPageUrl() . '">
                <TITLE>My new webpage</TITLE>
                </HEAD>
                <BODY>
                PLEASE WAIT!
                </BODY>
                </HTML>';
        $this->optionable[self::PARAM_CONCURRENT_CLIENTS_IN_THE_ROW] = 3;
    }

    /**
     *
     * @param $cache \POC\cache\cacheimplementation\Cache
     */
    public function __construct ($options = array())
    {
        $this->optionable = new Optionable($options);
        $this->setupDefaults($optionable);

        $this->maxNumberOfConcurrentConnections = $this->optionable[self::PARAM_CONCURRENT_CLIENTS_IN_THE_ROW];
        // $this->clientUnique =
        // $this->optionable[self::PARAM_CLIENT_UNIQUE];
    }

    public function setSentinel ($cnt)
    {
        $this->cache->cacheSpecificStore($this->getKey(), $cnt);
    }

    public function getSentinel ()
    {
        $sentinel = $this->cache->fetch($this->getKey());
        if (! $sentinel) {
            $sentinel = 0;
        }

        return ($sentinel);
    }

    private function getKey ()
    {
        return $this->poc->getHasher()->getKey() . self::KEY_POSTFIX;
    }

    public function deleteSentinel ()
    {
        $this->cache->clearItem($this->getKey());
//        $this->monoLogger->setLog(self::LOG_TYPE_CIA,
//                "deleted key:" . $this->getKey());
    }

    public function getPageUrl()
    {
        $servername = '';
        if (isset($_SERVER["SERVER_NAME"])) {
            $servername = $_SERVER["SERVER_NAME"];
        }
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $ru = "";
        if (isset($_SERVER["REQUEST_URI"])) {
            $ru = $_SERVER["REQUEST_URI"];
        }

        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $servername . ":" . $_SERVER["SERVER_PORT"] . $ru;
        } else {
            $pageURL .= $servername . $ru;
        }

        return $pageURL;
    }

    public function getRefreshPage ()
    {
        return $this->optionable[self::PARAM_REDIRECT_CONTENT];

    }

    public function consult (BaseEvent $event)
    {
        $sentinelCnt = $this->getSentinel();
        $this->setSentinel($sentinelCnt + 1);
        {
            if ($sentinelCnt) {
                $this->eventDispatcher->dispatch(
                        ROIProtectorEventNames::CONSULT_STARTED,
                        new ROIEvent($this));

                if ($sentinelCnt >= 1 and $sentinelCnt <= 2) {
                    while ($this->getSentinel()) {
//                        $this->monoLogger->setLog(self::LOG_TYPE_CIA,
//                                "Sleep: $sentinelCnt");
                        usleep(500000);
                    }
                    echo $this->poc->fetchCache();
                }
                if ($sentinelCnt >= $this->maxNumberOfConcurrentConnections) {
                    $this->outputHandler->ObPrintCallback(
                            $this->getRefreshPage());
                    $this->outputHandler->stopBuffer();
                }
            }
        }
//        $this->monoLogger->setLog(self::LOG_TYPE_CIA, "end: $sentinelCnt");
    }

    public function consultFinish (BaseEvent $event)
    {
        $this->deleteSentinel();
    }

   /**
    *
    * @param \Poc\Cache\CacheImplementation\Cache $cache
    */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }    
    
    public function setName() {
        $this->name = "ROIP";
    }
}
