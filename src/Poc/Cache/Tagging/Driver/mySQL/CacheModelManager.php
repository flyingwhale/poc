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

  public function getByTags(array $tags)
  {
    $tagsQuoted = array();
    foreach($tags as $tag)
    {
      $tagsQuoted[] = $this->getDbHandler()->quote($tag);
    }

    $tagsString = implode(",", $tagsQuoted);

    $dbh = $this->getDbHandler();

    $query = sprintf('SELECT c.* FROM caches c
      LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id)     
      LEFT JOIN tags t  ON (t.id = thc.tag_id)
      WHERE t.tag IN (%s)', $tagsString);

    $sth = $dbh->prepare($query);

    $sth->execute();

    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());

    return $models;
  }

  public function getByTagAndHash($tag, $hash)
  {
    $dbh = $this->getDbHandler();

    $query = 'SELECT caches.* FROM tags
            	LEFT JOIN tags_has_caches ON (tags.id = tags_has_caches.tag_id) 
            	LEFT JOIN caches ON (caches.id = tags_has_caches.cache_id) 
            	WHERE tags.tag = :tag AND caches.hash = :hash';

    $params = array(
    	':tag' => $tag,
    	':hash' => $hash    	
    );
    $sth = $dbh->prepare($query);

    $sth->execute($params);

    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());

    return $models;
  }

  public function getExpired($expires = null)
  {
    if (!$expires)
    {
      $expires = time();
    }

    $dbh = $this->getDbHandler();

    $query = 'SELECT caches.* FROM caches
             	WHERE expires < :expires';

    $params = array(':expires' => $expires);
    $sth = $dbh->prepare($query);

    $sth->execute($params);

    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());

    return $models;

  }

  public function deleteOrphans()
  {
    $dbh = $this->getDbHandler();
    $query = 'DELETE c FROM caches  c LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id) WHERE thc.cache_id IS NULL';
    $sth = $dbh->prepare($query);

    $sth->execute();
  }

  public function deleteWithRelationTag($hash)
  {
    $dbh = $this->getDbHandler();
    $query = 'DELETE c.*, thc.*, t.* FROM caches  c LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id) LEFT JOIN tags t  ON (t.id = thc.tag_id) WHERE c.hash = :hash';
    $params = array(':hash' => $hash);

    $sth = $dbh->prepare($query);
    $sth->execute($params);
  }

  public function deleteWithRelation($hash)
  {
    $dbh = $this->getDbHandler();
    $query = 'DELETE c.*, thc.* FROM caches  c LEFT JOIN tags_has_caches thc  ON (c.id = thc.cache_id) WHERE c.hash = :hash';
    $params = array(':hash' => $hash);
    $sth = $dbh->prepare($query);
    
    $sth->execute($params);

  }


}
?>