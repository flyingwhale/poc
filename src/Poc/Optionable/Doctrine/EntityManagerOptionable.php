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

namespace Poc\Optionable\Doctrine;

use Optionable;

class EntityManagerOptionable extends Optionable
{
    public function __construct (array $values = array())
    {
        parent::__construct($values);
        
        $this->setDefaultOption('entity_managers.default.conn_params.dbname', 'poc_tagging');
        $this->setDefaultOption('entity_managers.default.conn_params.user', 'poc_test');
        $this->setDefaultOption('entity_managers.default.conn_params.password', 'poc_test');
        $this->setDefaultOption('entity_managers.default.conn_params.host', 'localhost');
        $this->setDefaultOption('entity_managers.default.conn_params.driver', 'pdo_mysql');
        $this->setDefaultOption('entity_managers.default.conn_params',
            function ($c)
            {
                $connParams = array(
                    'dbname' => $c['entity_managers.default.conn_params.dbname'],
                    'user' => $c['entity_managers.default.conn_params.user'],
                    'password' => $c['entity_managers.default.conn_params.password'],
                    'host' => $c['entity_managers.default.conn_params.host'],
                    'driver' => $c['entity_managers.default.conn_params.driver']
                );

                return $connParams;
            }
        );
        
        
        

        $this->setDefaultOption('entity_managers.default.cache.class', '\Doctrine\Common\Cache\ArrayCache');
        $this->setDefaultOption('entity_managers.default.cache',
            function ($c)
                    {
                        $class = $c['entity_managers.default.cache.class'];
                        $cache = new $class();

                        return $cache;
                    }
         );

        
        $cache = $this['entity_managers.default.cache'];
        
        $this->setDefaultOption('entity_managers.default.config.class', '\Doctrine\ORM\Configuration');
        $this->setDefaultOption('entity_managers.default.config.proxy_dir_path', '/tmp');
        $this->setDefaultOption('entity_managers.default.config.proxy_namespace', 'Proxies');
        $this->setDefaultOption('entity_managers.default.config.auto_generate_proxy_classes', true);
        $this->setDefaultOption('entity_managers.default.config.default_annotation_driver', 'Configuration');
        $this->setDefaultOption('entity_managers.default.config.cache', $cache);
        $this->setDefaultOption('entity_managers.default.config', $this->share(
            function ($c)
            {
                $class = $c['entity_managers.default.config.class'];
                $config = new $class();

                // $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

                $proxyDirPath = $c['entity_managers.default.config.proxy_dir_path'];
                $config->setProxyDir($proxyDirPath);
                $config->setProxyNamespace($c['entity_managers.default.config.proxy_namespace']);
                $config->setAutoGenerateProxyClasses(true);

                $driverImpl = $config->newDefaultAnnotationDriver($c['entity_managers.default.config.default_annotation_driver']);
                $config->setMetadataDriverImpl($driverImpl);

                $cache = $c['entity_managers.default.config.cache'];
                $config->setMetadataCacheImpl($cache);
                $config->setQueryCacheImpl($cache);

                return $config;
            }
        ));

        $this->setDefaultOption('entity_managers.default',
            function ($c)
            {
                $connParams = $c['entity_managers.default.conn_params'];
                $config     = $c['entity_managers.default.config'];

                $entityManager = \Doctrine\ORM\EntityManager::create($connParams, $config);

                return $entityManager;
            }
        );

    }
}
?>
