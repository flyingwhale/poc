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

namespace Poc\Tests\Core\PluginSystem;

use \Mockery as m;
use Poc\Core\PluginSystem\PluginRegistry;
use Poc\Core\PluginSystem\MultipleSameTypePluginRegistrationExeption;

class PluginRegistryTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAddPlugin()
    {
        $plugin1 = m::mock('plugin',array('pluginInit'=>null,'getPluginName'=>'name','isPluginMultipleInstanced'=>false));
        $plugin1Delta = m::mock('plugin',array('pluginInit'=>null,'getPluginName'=>'name','isPluginMultipleInstanced'=>false));
        $plugin2 = m::mock('plugin',array('pluginInit'=>null,'getPluginName'=>'name1','isPluginMultipleInstanced'=>false));

        $pluginRegistry = new PluginRegistry();
        $pluginRegistry->addPlugin($plugin1);
        $pluginRegistry->addPlugin($plugin2);

        $this->setExpectedException('Poc\Core\PluginSystem\Exceptions\MultipleSameTypePluginRegistrationException');

        $pluginRegistry->addPlugin($plugin1Delta);

    }
    public function testGetPlugin()
    {
        $pluginRegistry = new PluginRegistry();
        $plugin = m::mock('plugin',array('init'=>null,'getPluginName'=>'name','isPluginMultipleInstanced'=>false));

        $pluginRegistry->addPlugin($plugin);

        $pluginRegistry->getPlugin('name');

        $this->setExpectedException('Poc\Core\PluginSystem\Exceptions\PluginIsNotRegisteredException');
        $pl = $pluginRegistry->getPlugin('noname');

        $this->assertEquals($plugin->getName(), $pl->getName());

        $pluginRegistry->getPlugin('noname');

    }
}
