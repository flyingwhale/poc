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
use Poc\Core\PluginSystem\Plugin;
use Poc\Poc;
use Poc\Events\BaseEvent;

class OutputFilter extends Plugin
{

    private $outputBlacklist = null;

    public function init(Poc $poc)
    {
        parent::init($poc);
        $poc->getPocDispatcher()->addListener(PocEventNames::BEFORE_THE_DECISION_IF_WE_CAN_STORE_THE_GENERATED_CONTENT,
                                           array($this, 'isOutputBlacklisted'));
    }

    public function addBlacklistCondition ($condition)
    {
        $this->outputBlacklist[] = $condition;
    }

    public function isOutputBlacklisted (BaseEvent $event)
    {
        if ($this->outputBlacklist) {
            foreach ($this->outputBlacklist as $condititon) {
                $result = preg_match($condititon, $this->poc->getOutput());
                if ($result) {
                  $this->poc->setCanICacheThisGeneratedContent(false);

                  return;
                }
            }
        }
    }
}
