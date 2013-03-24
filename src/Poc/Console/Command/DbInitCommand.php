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

namespace Poc\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Poc\Optionable\DoctrineOptionable;

class DbInitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('poc:db:init')
            ->setDescription('Initialize database tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $connectionName = 'mysql';
        $doctrineOptionable = new DoctrineOptionable();

        $doctrineOptionable['dbal.connections.mysql.dbname'] =  'poc_tests';
        $doctrineOptionable['dbal.connections.mysql.user'] =  'root';
        $doctrineOptionable['dbal.connections.mysql.password'] =  'qweasd';
        $doctrineOptionable['dbal.connections.mysql.host'] =  'localhost';
        $doctrineOptionable['dbal.connections.mysql.driver'] =  'pdo_mysql';

        $this->initDatabase($connectionName, $doctrineOptionable, $output);
        $connectionName = 'sqlite';
        $this->initDatabase($connectionName, $doctrineOptionable, $output);

    }

    protected function cloneSchemaWithoutForeignKeys($schema)
    {
        $tables = array();
        foreach ($schema->getTables() as $table) {

            foreach ($table->getForeignKeys() as $fkName => $fk) {

                $table->removeForeignKey($fkName);

            }
            $tables[] = $table;

        }

        $newSchema = new Schema($tables);

        return $newSchema;
    }

    protected function getTaggingSchema($em)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array(
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Cache'),
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\CacheTag'),
        $em->getClassMetadata('Poc\PocPlugins\Tagging\Driver\Doctrine2\Entities\Tag')

        );

        $schema = $tool->getSchemaFromMetadata($classes);

        return $schema;
    }

    protected function getDiffSchema($fromSchema, $toSchema)
    {
        $comparator = new Comparator();
        $diffSchema = $comparator->compare($fromSchema, $toSchema);

        return $diffSchema;
    }

    protected function executeQueries($conn, $sqls)
    {
            foreach ($sqls as $sql) {
                $conn->executeQuery($sql);
            }

    }

    protected function initDatabase($connectionName, $doctrineOptionable, $output)
    {
        $doctrineOptionable['orm.entity_managers.default.connection'] =  $connectionName;

        $connectionOptions = $doctrineOptionable['dbal.connections.'.$connectionName];

        $text = sprintf('Connection name: %s', $connectionName);
        $output->writeln($text);
        $text = sprintf('Connection options:');
        $output->writeln($text);

        foreach ($connectionOptions as $connectionOptionKey => $connectionOptionValue) {
            $text = sprintf(' - %s: %s', $connectionOptionKey, $connectionOptionValue);
            $output->writeln($text);
        }
        $dialog = $this->getHelperSet()->get('dialog');
        $initDatabase = false;
        if ($dialog->askConfirmation($output, '<question>Init database?</question>', false)) {
            $initDatabase = true;
        }

        $em = $doctrineOptionable['orm.entity_managers.default'];
        $conn = $em->getConnection();

        $sm = $conn->getSchemaManager();
        $fromSchema = $sm->createSchema();

        $toSchema = $this->getTaggingSchema($em);
        $toSchema = $this->cloneSchemaWithoutForeignKeys($toSchema);

//        $schemaDiff = $this->getDiffSchema($fromSchema, $toSchema);
//        $sqlDiff = $schemaDiff->toSql($conn->getDatabasePlatform());
//        if (!empty($sqlDiff))

        if ($initDatabase) {
            $output->writeln('Drop and create tables');

            $dropSqls = $fromSchema->toDropSql($conn->getDatabasePlatform());
            $createSqls = $toSchema->toSql($conn->getDatabasePlatform());

            $this->executeQueries($conn, $dropSqls);
            $this->executeQueries($conn, $createSqls);
        }

    }

}
