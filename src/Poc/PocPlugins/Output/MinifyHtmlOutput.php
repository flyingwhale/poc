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

namespace Poc\PocPlugins\Output;

use Poc\Core\Events\BaseEvent;
use FlyingWhale\Plugin\PluginInterface;
use Poc\Toolsets\NativeOutputHandlers\Handlers\Callback\CallbackHandlerEventNames;

class MinifyHtmlOutput implements PluginInterface
{
    public function init($poc)
    {
        $poc->getPocDispatcher()->addListener(CallbackHandlerEventNames::BEFORE_STORE_OUTPUT, array($this, 'minifyHtml'));
    }

    public function minifyHtml(BaseEvent $event)
    {
        $search =
                array(
                    '/ {2,}/',
                    '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s',
                    '/\>[^\S ]+/s', //strip whitespaces after tags, except space
                    '/[^\S ]+\</s', //strip whitespaces before tags, except space
                    '/(\s)+/s', // shorten multiple whitespace sequences
        );            // shorten multiple whitespace sequences
        $replace =
                array(
                    ' ',
                    ' ',
                    '>',
                    '<',
                    '\\1',
        );
        $event->getPoc()->setOutput(
                preg_replace($search, $replace, $event->getPoc()->getOutput()));
    }

    public function getName()
    {
        return "MinifyHtmlOutput";
    }

    public function isMultipleInstanced()
    {
        return false;
    }
}
