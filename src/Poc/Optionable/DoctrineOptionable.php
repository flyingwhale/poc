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

namespace Poc\Optionable;

use Optionable;

class DoctrineOptionable extends Optionable
{

    public function __construct (array $values = array())
    {
        parent::__construct($values);

        $this->setDefaultOption('dbal.default_connection', 'sqlite');
        $this->setDefaultOption('dbal.connections.sqlite.path', '/tmp/poc.sq3');
        $this->setDefaultOption('dbal.connections.sqlite.driver', 'pdo_sqlite');
        $this->setDefaultOption('dbal.connections.sqlite',
            function ($c) {
                $connParams = array(
                    'path' => $c['dbal.connections.sqlite.path'],
                    'driver' => $c['dbal.connections.sqlite.driver']
                );

                return $connParams;
            }
        );

        $this->setDefaultOption('dbal.connections.mysql.dbname', 'poc_tagging');
        $this->setDefaultOption('dbal.connections.mysql.user', 'poc_test');
        $this->setDefaultOption('dbal.connections.mysql.password', 'password');
        $this->setDefaultOption('dbal.connections.mysql.host', 'localhost');
        $this->setDefaultOption('dbal.connections.mysql.driver', 'pdo_mysql');
        $this->setDefaultOption('dbal.connections.mysql',
            function ($c) {
                $connParams = array(
                    'dbname' => $c['dbal.connections.mysql.dbname'],
                    'user' => $c['dbal.connections.mysql.user'],
                    'password' => $c['dbal.connections.mysql.password'],
                    'host' => $c['dbal.connections.mysql.host'],
                    'driver' => $c['dbal.connections.mysql.driver']
                );

                return $connParams;
            }
        );

        $this->setDefaultOption('orm.default_entity_manager', 'default');
        $this->setDefaultOption('orm.entity_managers.default.connection', 'sqlite');

        $this->setDefaultOption('orm.entity_managers.default.config.auto_generate_proxy_classes', true);
        $this->setDefaultOption('orm.entity_managers.default.config.proxy_namespace', 'Proxies');
        $this->setDefaultOption('orm.entity_managers.default.config.proxy_dir', '/tmp');

        $this->setDefaultOption('orm.entity_managers.default.cache.class', '\Doctrine\Common\Cache\ArrayCache');
        $this->setDefaultOption('orm.entity_managers.default.cache',
            function ($c) {
                        $class = $c['orm.entity_managers.default.cache.class'];
                        $cache = new $class();

                        return $cache;
                    }
         );

        $this->setDefaultOption('orm.entity_managers.default.config.class', '\Doctrine\ORM\Configuration');

        $this->setDefaultOption('orm.entity_managers.default.config.default_annotation_driver', 'Configuration');
        $this->setDefaultOption('orm.entity_managers.default.config', $this->share(
            function ($c) {
                $class = $c['orm.entity_managers.default.config.class'];
                $config = new $class();

                // $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

                $proxyDirPath = $c['orm.entity_managers.default.config.proxy_dir'];
                $config->setProxyDir($proxyDirPath);
                $config->setProxyNamespace($c['orm.entity_managers.default.config.proxy_namespace']);
                $config->setAutoGenerateProxyClasses(true);

                $driverImpl = $config->newDefaultAnnotationDriver($c['orm.entity_managers.default.config.default_annotation_driver']);
                $config->setMetadataDriverImpl($driverImpl);

                $cache = $c['orm.entity_managers.default.cache'];
                $config->setMetadataCacheImpl($cache);
                $config->setQueryCacheImpl($cache);

                return $config;
            }
        ));

        $this->setDefaultOption('orm.entity_managers.default',
            function ($c) {

                $connectionParamName = 'dbal.connections.'.$c['orm.entity_managers.default.connection'];

                $connParams = $c[$connectionParamName];
                $config     = $c['orm.entity_managers.default.config'];

                $entityManager = \Doctrine\ORM\EntityManager::create($connParams, $config);

                return $entityManager;
            }
        );
    }
}
