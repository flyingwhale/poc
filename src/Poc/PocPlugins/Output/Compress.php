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

namespace Poc\PocPlugins\Output;

use Poc\PocEvents\PocEventNames;
use Poc\Poc;

use Poc\Events\BaseEvent;
use Poc\Core\PluginSystem\Plugin;

class Compress extends \Poc\Core\PluginSystem\Plugin
{

    const COMPRESSION_NONE = 'text/html';
    const COMPRESSION_DEFLATE = 'deflate';
    const COMPRESSION_GZIP = 'gzip';

    private $compressionType;


    public function init (Poc $poc)
    {
        parent::init($poc);

        $this->setCompressiontype($poc);

        $poc->getOutputHandler()->header('Content-Encoding: ' . $this->compressionType);

        $poc->getPocDispatcher()->addListener(PocEventNames::COMPRESS_OUTPUT,
                                                    array($this, 'compress'));

        $poc->getPocDispatcher()->addListener(PocEventNames::FUNCTION_FETCHCACHE_BEGINING,
                                                    array($this, 'modifyHasher'));

    }

    private function setCompressiontype(Poc $poc){
        $headers = $poc->getOutputHandler()->getallheaders();
        if(isset($headers['Accept-Encoding']))
        {
            if( strstr($headers['Accept-Encoding'], self::COMPRESSION_GZIP) )
            {
                $this->compressionType = self::COMPRESSION_GZIP;

            }
        }
        //$this->compressionType = self::COMPRESSION_DEFLATE;
    }

    public function modifyHasher(BaseEvent $event)
    {
        $event->getEvent()->getHasher()->addDistinguishVariable($this->compressionType);
    }

    public function compress (BaseEvent $event)
    {
        $event->getEvent()->getOutputHandler()->header('Content-Encoding: gzip');
        if($this->compressionType == self::COMPRESSION_GZIP){
            $event->getEvent()->setOutput(\gzencode($event->getEvent()->getOutput(), 9, FORCE_GZIP));
        }
        elseif($this->compressionType == self::COMPRESSION_DEFLATE){
            $event->getEvent()->setOutput(\gzdeflate($event->getEvent()->getOutput()));
        }
    }
}
