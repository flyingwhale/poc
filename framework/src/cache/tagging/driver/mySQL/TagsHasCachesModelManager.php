<?php
namespace POC\cache\tagging\driver\mysql;

class TagsHasCachesModelManager extends ModelManager
{
  public function __construct($dbHandler)
  {
    parent::__construct($dbHandler);
    $this->setModelName('\\POC\\cache\\tagging\\driver\\mysql\\model\\TagCache');
    $this->setTableName('tags_has_caches');
    $this->setIdName(null);
    
    $this->setQuery('create', 'CREATE TABLE IF NOT EXISTS `tags_has_caches` (
        `tag_id` int(11) NOT NULL,
        `cache_id` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8');
  }
}
?>