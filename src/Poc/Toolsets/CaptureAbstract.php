<?php
/*
 * Copyright 2013 Imre Toth <tothimre at gmail> Licensed under the Apache
 * License, Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law
 * or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */
namespace Poc\Toolsets;

use \Poc\Core\PocEvents\PocEventNames;

abstract class  CaptureAbstract implements CaptureIntrerface 
{
    
    const NAME_START="CaptureInterface";
    
    protected $poc;
    
    protected $pocDispatcher;
    
    /**
     * 
     * @param \Poc\Poc $pluginContainer
     */
    public function init($pluginContainer)
    {
        $this->poc = $pluginContainer;
        
        $this->pocDispatcher = $this->poc->getPocDispatcher();
 
        $this->pocDispatcher->addListener( PocEventNames::GET_OUTPUT_FROM_CACHE,
                                            array($this, 'getOutputFromCache'));

        $this->pocDispatcher->addListener(
                                PocEventNames::CAPTURE,array($this, 'capture'));

        $this->pocDispatcher->addListener(
                                PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                    array($this, 'setObLevel'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::MONITOR,
                                                    array($this, 'monitor'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::END_OF_BUFFERING,
                                                array($this, 'endOfBuffering'));
    }
    
    public function getName()
    {
        $rnd = rand(0, 1);
        return self::NAME_START.$rnd;
    }
    
    public function isMultipleInstanced()
    {
        return false;
    }
}
