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

if(function_exists('xdebug_disable'))
{
    xdebug_disable();
    echo("xdebug disabled-----------------");
}

require_once 'vendor/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$namespaces = array('Poc\\Tests' => __DIR__);
$loader->registerNamespaces($namespaces);

$loader->register();

$GLOBALS['DOCTRINE_OPTIONABLE'] = array(
    'orm.entity_managers.default.connection'    => $GLOBALS['CONNECTION_DEFAULT'],
    'dbal.connections.mysql.driver' => 'pdo_mysql',
    'dbal.connections.mysql.host' => $GLOBALS['CONNECTION_MYSQL_HOST'],
    'dbal.connections.mysql.dbname' => $GLOBALS['CONNECTION_MYSQL_DBNAME'],
    'dbal.connections.mysql.user' => $GLOBALS['CONNECTION_MYSQL_USER'],
    'dbal.connections.mysql.password' => $GLOBALS['CONNECTION_MYSQL_PASS'],

    'dbal.connections.sqlite.path' => $GLOBALS['CONNECTION_SQLITE_PATH'],
    'dbal.connections.sqlite.driver' => 'pdo_sqlite'
);
