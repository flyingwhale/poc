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
 * This class is an implementation of the Outoput Interface.
 * This is used in a
 * server Environment to handle the output.
 *
 * @author Imre Toth
 *
 */
namespace Poc\Handlers;

class ServerOutput extends Output
{

    public function getLevel ()
    {
        return ob_get_level();
    }

    public function startBuffer ($callbackFunctname)
    {
        ob_start(array($this->poc, $callbackFunctname));
    }

    public function StopBuffer ($output = '')
    {
        die($output);
    }

    public function header ($header)
    {
        header($header);
    }

    public function obEnd ()
    {
        ob_end_flush();
    }

    public function ObPrintCallback ($output)
    {
        echo $output;
    }

    public function headersList ()
    {
        return \headers_list();
    }

    public function printOutputCallback ($output)
    {
        echo $output;
    }
}
