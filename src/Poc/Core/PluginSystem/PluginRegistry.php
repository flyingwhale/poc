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
/**
 * @author Imre Toth
 *
 */
namespace Poc\Core\PluginSystem;

class PluginRegistry
{
    private $singleInstancedPlugins = array("a","b","c");
    
    public function addPlugin($plugin)
    {
//        
//                        echo"
//--------".                    
//$plugin->getName()
//."********
//";
                        
        $this->singleInstancedPlugins = null;
        
        //if($plugin->isMultipleInstanced())
        {
            if (isset($this->singleInstancedPlugins[$plugin->getName()]))
            {
               throw (new \Exception("Please Don't register this plugin twice!")); 
            }
            else
            {
                $this->singleInstancedPlugins[$plugin->getName()] = $plugin;
            }
        }        
    }
    
    public function getPlugin($name)
    {
        echo "
            


".$name."   



";
        
        var_dump($this->singleInstancedPlugins);
        echo $name."   
            ";
        if (isset($this->singleInstancedPlugins[$name]))
        {
           return $this->singleInstancedPlugins[$name];
        }
        else
        {
           throw (new \Exception("Plugin is not registered! ")); 
        }
    }
}