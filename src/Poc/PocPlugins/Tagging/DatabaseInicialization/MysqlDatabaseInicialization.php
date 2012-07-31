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

namespace Poc\PocPlugins\Tagging\DatabaseInicialization;

class MysqlDatabaseInicialization
{

    private $pdo = null;
    private $db = null;

    public function __construct($pdo, $db)
    {
        $this->pdo = $pdo;
        $this->db = $db;

    }

    public function create()
    {
        $this->createDb();
        $this->createTables();
    }

    public function createDb ()
    {
        $query = 'CREATE DATABASE `' . $this->db . '` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
        $this->pdo->exec($query);
        $query = 'USE ' . $this->db;
        $this->pdo->exec($query);
    }

    public function createTables()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tag` char(10) NOT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
        $this->pdo->exec($query);

        $query = 'CREATE TABLE IF NOT EXISTS `tags_has_caches` (
        `tag_id` int(11) NOT NULL,
        `cache_id` int(11) NOT NULL,
        PRIMARY KEY (`tag_id`,`cache_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8';
        $this->pdo->exec($query);

        $query = 'CREATE TABLE IF NOT EXISTS `caches` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `hash` char(64) NOT NULL,
                `expires` int(32) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
        $this->pdo->exec($query);

    }
}
