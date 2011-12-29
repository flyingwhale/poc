<?php
namespace POC\cache\tagging\driver\mysql;

class ModelManager
{
  protected $dbHandler;
  protected $idName;
  protected $findOneByQuery;
  protected $querys;
  protected $modelName;
  protected $tableName;
  
  public function __construct($dbHandler)
  {
    $this->setDbHandler($dbHandler);
    $this->setIdName('id');
    
    $this->setQuery('findAll', 'SELECT * FROM %s');
    $this->setQuery('findBy', 'SELECT * FROM %s WHERE %s = :%s');
    $this->setQuery('findOneBy', 'SELECT * FROM %s WHERE %s = :%s LIMIT 1');
    $this->setQuery('delete', 'DELETE FROM %s WHERE %s = :%s');
    $this->setQuery('truncate', 'TRUNCATE TABLE %s');
    
  }

  public function createTable()
  {
    $queryName = 'create';
    
    if ($this->hasQuery($queryName))
    {
      $dbh = $this->getDbHandler();
      $query = $this->getQuery($queryName);
      $sth = $dbh->prepare($query);
      $sth->execute();
    }
  }
  
  public function truncateTable()
  {
    $queryName = 'truncate';
    if ($this->hasQuery($queryName))
    {
      $dbh = $this->getDbHandler();
      $query = sprintf(
        $this->getQuery($queryName),
        $this->getTableName()  
      );
      $sth = $dbh->prepare($query);
      $sth->execute();
      
    }
    
  }
  public function getQuery($name)
  {
    return $this->querys[$name];
  }
  
  public function setQuery($name, $value)
  {
    $this->querys[$name] = $value;    
  }
  
  public function hasQuery($name)
  {
    if (isset($this->querys[$name]))
    {
      return true;
    }
    return false;
  }
  
  public function getDbHandler()
  {
    return $this->dbHandler;
  }
  
  public function setDbHandler($dbHandler)
  {
    $this->dbHandler = $dbHandler;
  }

  public function getIdName()
  {
    return $this->idName;
  }
  
  public function setIdName($idName)
  {
    $this->idName = $idName;
  }
  
  public function getModelName()
  {
    return $this->modelName;
  }
  
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }

  public function getTableName()
  {
    return $this->tableName;
  }
  
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }
  
  private function __findBy($fieldName, $value, $query)
  {
    $dbh = $this->getDbHandler();
    
    $params = array(':'.$fieldName => $value);
    $sth = $dbh->prepare($query);
    
    $sth->execute($params);
    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());

    return $models;
  }
  
  public function findBy($fieldName, $value)
  {
    $query = sprintf($this->getQuery('findBy') , $this->getTableName(), $fieldName, $fieldName);
    return $this->__findBy($fieldName, $value, $query);
  }

  public function findOneBy($fieldName, $value)
  {
    $query = sprintf($this->getQuery('findOneBy') , $this->getTableName(), $fieldName, $fieldName);
    
    $models = $this->__findBy($fieldName, $value, $query);
    if (!empty($models))
    {
      return $models[0];
    }
    return $models;
  }
  
  public function findAll()
  {
    $dbh = $this->getDbHandler();
    
    $query = sprintf($this->getQuery('findAll') , $this->getTableName());
    
    $sth = $dbh->prepare($query);
    
    $sth->execute();
    $models = $sth->fetchAll(\PDO::FETCH_CLASS, $this->getModelName());
    
    return $models;
    
  }
  
  public function find($id)
  {
    return $this->findOneBy($this->getIdName(), $id);
  }
  
  public function delete($id)
  {
    $dbh = $this->getDbHandler();
    
    $fieldName = $this->getIdName();
    $query = sprintf($this->getQuery('delete'), $this->getTableName(), $fieldName, $fieldName);
    $params = array(':'.$fieldName => $id);
    
    $sth = $dbh->prepare($query);
    $sth->execute($params);
    
  }
  
  public function saveAll($models)
  {
    foreach($models as $key => $model)
    {
      $this->__save($models[$key]);
    }
  
    return $models;
  }
  
  public function save($models)
  {
    if (!is_array($models))
    {
      $this->__save($models);
    }
    else
    {
      $this->saveAll($models);
  
    }
  
    return $models;
  }

  protected function __save($model)
  {
    $dbh = $this->getDbHandler();
  
    $query = 'REPLACE INTO %s (%s) VALUES (%s)';
    $params = array();

    $fieldValues = array();
    foreach($model as $key => $value)
    {
      if (empty($value))
      {
        unset($fieldValues[$key]);
      }
      else
      {
        $fieldValues[$key] = ':'.$key;
        $params[':'.$key] = $value;
        
      }
    }
    
    $fieldNamesString = implode(",", array_keys($fieldValues));
    $valueNamesString = implode(",", array_values($fieldValues));
    
    $query = sprintf($query, $this->getTableName(), $fieldNamesString, $valueNamesString);
//    error_log(self::showQuery($query, $params), null, 0);
    
    
    $sth = $dbh->prepare($query);
  
    $sth->execute($params);
  
    $idName = $this->getIdName('id');
    if ($idName && !$model->$idName)
    {
      $model->$idName = $dbh->lastInsertId();
    }
  
    return $model;
  }
  
//    echo self::showQuery($query, $params);  
  public static function showQuery($query, $params)
  {
    $keys = array();
    $values = array();
  
    # build a regular expression for each parameter
    foreach ($params as $key=>$value)
    {
      if (is_string($key))
      {
        $keys[] = '/'.$key.'/';
      }
      else
      {
        $keys[] = '/[?]/';
      }
      if(is_numeric($value))
      {
        $values[] = intval($value);
      }
      else
      {
        $values[] = '"'.$value .'"';
      }
    }
  
    $query = preg_replace($keys, $values, $query, 1, $count);
  
    return $query;
  }
  
}
?>