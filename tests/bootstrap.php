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

require_once 'vendor/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$namespaces = array('Poc\\Tests' => __DIR__);
$loader->registerNamespaces($namespaces);

$prefixes = array('Rediska' => __DIR__ . '/../vendor/rediska/library');
$loader->registerPrefixes($prefixes);

$loader->register();

$pdo = new \PDO($GLOBALS['MYSQL_DSN'], $GLOBALS['MYSQL_USER'], $GLOBALS['MYSQL_PASS']);
$dbInit = new \Poc\Cache\Tagging\DatabaseInicialization\MysqlDatabaseInicialization($pdo, $GLOBALS['MYSQL_DBNAME']);
$dbInit->create();

