<?php
namespace POC\cache\tagging\driver\mysql;

class CacheModelManager extends ModelManager
{
  public function __construct($dbHandler)
  {
    parent::__construct($dbHandler);
    
    $this->setModelName('\\POC\\cache\\tagging\\driver\\mysql\\model\\Cache');
    $this->setTableName('caches');
    $this->setQuery('create', 'CREATE TABLE IF NOT EXISTS `caches` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `hash` char(64) NOT NULL,
				`expires` int(32) NOT NULL,      
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
  }
  
  public function getByTag($tag)
  {
    $dbh = $this->getDbHandler();
  
    $query = 'SELECT caches.* FROM tags
          	LEFT JOIN tags_has_caches ON (tags.id = tags_has_caches.tag_id) 
          	LEFT JOIN caches ON (caches.id = tags_has_caches.cache_id) 
          	WHERE tags.tag = :tag';
  
    $params = array(':tag' => $tag);
    $sth = $dbh->prepare($query);
  
    $sth->execute($params);
  
    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());
  
    return $models;
  
  }
  
}
?>