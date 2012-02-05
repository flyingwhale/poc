<?php
namespace Poc\Cache\Tagging\Driver\Mysql;

class TagsHasCachesModelManager extends ModelManager
{
  public function __construct($dbHandler)
  {
    parent::__construct($dbHandler);
    $this->setModelName('\\Poc\\Cache\\Tagging\\Driver\\Mysql\\TagCache');
    $this->setTableName('tags_has_caches');
    $this->setIdName(null);
    
    $this->setQuery('create', 'CREATE TABLE IF NOT EXISTS `tags_has_caches` (
        `tag_id` int(11) NOT NULL,
        `cache_id` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8');
  }

  public function find($tag_id, $cache_id)
  {
    $dbh = $this->getDbHandler();
    
    $query = 'SELECT * FROM tags_has_caches    
    	WHERE tag_id = :tag_id AND cache_id = :cache_id
    	LIMIT 1';
    
    $params = array(
    ':tag_id' => $tag_id,
    ':cache_id' => $cache_id
    );
    $sth = $dbh->prepare($query);
    
    $sth->execute($params);
    
    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());
    
    if (!empty($models))
    {
      return $models[0];
    }
    return $models;
        
  }
  
  public function getExpired($expires = null)
  {
    if (!$expires)
    {
      $expires = time();
    }
  
    $dbh = $this->getDbHandler();
  
    $query = 'SELECT tags_has_caches.* FROM tags_has_caches
			LEFT JOIN caches ON (caches.id = tags_has_caches.cache_id)     
      WHERE caches.expires < :expires';
  
    $params = array(':expires' => $expires);
    $sth = $dbh->prepare($query);
  
    $sth->execute($params);
    
    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());
  
    return $models;
  
  }
  
  public function delete(\POC\cache\tagging\driver\mysql\model\TagCache $model)
  {
    $dbh = $this->getDbHandler();
    
    $query = sprintf('DELETE FROM %s WHERE cache_id = :cache_id AND tag_id = :tag_id', $this->getTableName());
    
    $params = array(
            ':cache_id' => $model->cache_id,
            ':tag_id' => $model->tag_id
    );
    $sth = $dbh->prepare($query);
    
    $sth->execute($params);
    
  }
  
  public function deleteByHashTags($hash, $tags)
  {
    $tagsQuoted = array();
    foreach($tags as $tag)
    {
      $tagsQuoted[] = $this->getDbHandler()->quote($tag);
    }
    
    $tagsString = implode(",", $tagsQuoted);
    
    $dbh = $this->getDbHandler();
    
    $query = sprintf('DELETE ths FROM tags_has_caches ths
    		LEFT JOIN tags t  ON (t.id = ths.tag_id)
    		LEFT JOIN caches c  ON (c.id = ths.cache_id)     
    	    WHERE c.hash = :hash AND t.tag IN (%s)',
    $tagsString
    );
    
    $params = array(
        ':hash' => $hash
    );
    
    $sth = $dbh->prepare($query);
    
    $sth->execute($params);
    
  }
  
  public function deleteOrphans()
  {
    $dbh = $this->getDbHandler();
    $query = 'DELETE ths FROM tags_has_caches ths  LEFT JOIN tags t  ON (t.id = ths.tag_id) LEFT JOIN caches c  ON (c.id = ths.cache_id) WHERE  t.id IS NULL OR c.id IS NULL';
    $sth = $dbh->prepare($query);
  
    $sth->execute();
  }
  
}
?>