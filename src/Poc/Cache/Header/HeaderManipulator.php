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
 * This class contains some header related functionality.
 * By utilizing its
 * capabilities you will be able to manipulate and store the the header.
 *
 * @author Imre Toth
 *
 */
namespace Poc\Cache\Header;

class HeaderManipulator
{

    const HEADER_POSTFIX = "_HE";

    public $headersToPreserve;

    public $headersToStore;

    public $headersToSend;

    public $headersToRemove;

    /**
     *
     * @var \Poc\Poc
     */
    private $poc;

    public $isEtagGeneration;

    /*
     * public function __construct(Poc $poc) { $this->poc = $poc; }
     */

    public function setPoc ($poc)
    {
        $this->poc = $poc;
    }

    public function storeHeaderToRemove ($headerVariable)
    {
        $this->headersToRemove[] = $headerVariable;
    }

    public function removeHeaders ()
    {
        if ($this->headersToRemove) {
            foreach ($this->headersToRemove as $removeThisHeader) {
                header_remove($removeThisHeader);
            }
        }
    }

    public function storeHeaderVariable ($headerVariable)
    {
        // TODO: check for all possible valid header variables.
        $this->headersToPreserve[] = $headerVariable;
    }

    public function storeHeadersForPreservation ($responseHeaders)
    {
        //if ($this->headersToPreserve)
        {
            $headerTmp = array();

            foreach ($responseHeaders as $header) {
                $headerTmp[] = explode(':', $header);
            }

            //foreach ($this->headersToPreserve as $findThisHeader) {
                foreach ($headerTmp as $preserveThisHeader)
                {
                    //if ($preserveThisHeader[0] == $findThisHeader)
                    {
//                        $this->headersToStore[] = $findThisHeader . ': ' .
                        $this->headersToStore[] = $preserveThisHeader[0] . ': ' .
                                                         $preserveThisHeader[1];
                    }
                }
            //}
        }
    }

    public function storeHeaders ()
    {
        if ($this->headersToStore)
        {
            $this->poc->getCache()->cacheSpecificStore(
                    $this->poc->getHasher()
                    ->getKey() .
                        self::HEADER_POSTFIX, serialize($this->headersToStore));
            $this->poc->getLogger()->setLog("headers_store", serialize($this->headersToStore));

        }
    }

    public function fetchHeaders ()
    {
        $this->headersToSend = unserialize( $this->poc->getCache()->fetch(
                                              $this->poc->getHasher()->getKey().
                                                         self::HEADER_POSTFIX));

        $this->poc->getLogger()->setLog("headers__", serialize($this->headersToSend));

        if ($this->headersToSend) {
            foreach ($this->headersToSend as $header) {
                $this->poc->getOutputHandler()->header($header);
            }
        }
    }

}
