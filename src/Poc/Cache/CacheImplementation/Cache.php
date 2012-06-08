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
 * This is the root class of the Cache engines that the framework implements.
 *
 * @author Imre Toth
 *
 */
namespace Poc\Cache\CacheImplementation;


use Poc\Poc;
use Poc\Cache\Tagging\AbstractDb;
use Poc\Cache\CacheImplementation\CacheParams;
use Optionable;

abstract class Cache implements CacheInterface, CacheParams
{

    /**
     * This variable must be declared at the constructors of this class.
     */
    protected $ttl;

    protected $defaultOptions = array();

    /**
     *
     * @var Optionable
     */
    protected $optionable = null;

    protected function setupDefaults ()
    {

        $this->optionable->setDefaultOption(self::PARAM_TTL, 5);
    }
    
    public function __construct ($options)
    {
        $this->optionable =  new Optionable($options);
        $this->setupDefaults();
        $this->ttl = $this->optionable[CacheParams::PARAM_TTL];

    }

    public function throwDbException ()
    {
        if (! $this->isCacheAvailable()) {
            throw new \Exception("The cache database is not reachable!");
        }
    }

    public function getTtl()
    {
        return $this->ttl;
    }

}
