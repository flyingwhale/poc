<?php
namespace Poc\Cache\Tagging\Driver\Mysql;

class TagModelManager extends ModelManager
{

    public function __construct ($dbHandler)
    {
        parent::__construct($dbHandler);
        $this->setModelName('\\Poc\\Cache\\Tagging\\Driver\\Mysql\\Tag');
        $this->setTableName('tags');
        $this->setQuery('create',
                'CREATE TABLE IF NOT EXISTS `tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tag` char(10) NOT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
    }

    public function getByHash ($hash)
    {
        $dbh = $this->getDbHandler();

        $query = 'SELECT tags.* FROM tags
        LEFT JOIN tags_has_caches ON (tags.id = tags_has_caches.tag_id)
        LEFT JOIN caches ON (caches.id = tags_has_caches.cache_id)
        WHERE caches.hash = :hash';

        $params = array(':hash' => $hash);
        $sth = $dbh->prepare($query);

        $sth->execute($params);

        $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());

        return $models;
    }

    public function deleteOrphans ()
    {
        $dbh = $this->getDbHandler();
        $query = 'DELETE t FROM tags  t LEFT JOIN tags_has_caches thc  ON (t.id = thc.tag_id) WHERE thc.tag_id IS NULL';
        $sth = $dbh->prepare($query);

        $sth->execute();
    }

    public function deleteWithRelationCache ($tag)
    {
        $dbh = $this->getDbHandler();
        $query = 'DELETE c.*, thc.*, t.* FROM caches  c LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id) LEFT JOIN tags t  ON (t.id = thc.tag_id) WHERE t.tag = :tag';
        $params = array(':tag' => $tag);

        $sth = $dbh->prepare($query);
        $sth->execute($params);
    }

}
?>
