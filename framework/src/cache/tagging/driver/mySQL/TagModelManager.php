<?php
namespace POC\cache\tagging\driver\mysql;

class TagModelManager extends ModelManager
{
  public function __construct($dbHandler)
  {
    parent::__construct($dbHandler);
    $this->setModelName('\\POC\\cache\\tagging\\driver\\mysql\\model\\Tag');
    $this->setTableName('tags');
    $this->setQuery('create', 'CREATE TABLE IF NOT EXISTS `tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tag` char(10) NOT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
  }
  
  public function getByHash($hash)
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
}
?>