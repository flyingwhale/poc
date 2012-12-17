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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Poc;
use Optionable;

class ROIProtectorLogger implements ROIProtectorEventNames
{

    /**
     *
     * @var EventDispatcher
     */
    private $pocDispatcher;

    /**
     *
     * @var Optionable
     *
     */
    private $optionable;

    /**
     *
     * @var  Poc\Poc;
;
     */
    private $poc;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    public function setupDefaults ()
    {
        $this->optionable->setDefaultOption('poc', null);

    }

    public function __construct ($options = array())
    {

        $this->optionable = new Optionable($options, $this);
        $this->setupDefaults();

        $this->poc = $this->optionable[PocLogsParams::PARAM_POC];
        $this->logger = $this->poc->getLogger();

        $this->pocDispatcher = $this->poc->getPocDispatcher();

        $this->pocDispatcher->addListener(
                ROIProtectorEventNames::CONSULT_STARTED,
                array($this, 'consultLogger'));

        $this->pocDispatcher->addListener(
                ROIProtectorEventNames::CONSULT_STARTED_NOT_FIRST,
                array($this, 'consultFirstLogger'));

        $this->pocDispatcher->addListener(
                ROIProtectorEventNames::CONSULT_STARTED_FIRST,
                array($this, 'consultNotFirstLogger'));

        $this->pocDispatcher->addListener(ROIProtectorEventNames::CONSULT_SLEEP,
                array($this, 'consultSleep'));

        // 'I am sleeping '.$sentinelCnt);
    }

    public function consultLogger (ROIEvent $event)
    {
        $this->logger->setLog(ROIProtector::LOG_TYPE_CIA,
                'sentiel after inc cnt: ' . $event->getCia()
                    ->getSentinel());
    }

    public function consultFirstLogger (ROIEvent $event)
    {
        $this->logger->setLog(ROIProtector::LOG_TYPE_CIA, 'FIRST');
    }

    public function consultNotFirstLogger (ROIEvent $event)
    {
        $this->logger->setLog(ROIProtector::LOG_TYPE_CIA, 'NOT FIRST');
    }

    public function consultSleep (ROIEvent $event)
    {
        $this->logger->setLog(ROIProtector::LOG_TYPE_CIA,
                'I am sleeping: ' . $event->getCia()
                    ->getSentinel());
    }

}
