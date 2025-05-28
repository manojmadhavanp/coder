<?php


namespace System\MVC;

use System\Database\DBServer;
use System\Database\DBCollection;
use System\Core\Logger;

abstract class CollectionController
{
    protected $dbCollection;
    protected $tableName;

    public function __construct()
    {
        $dbServer = DBServer::connect(); // Use the default connection
        $this->tableName = $this->getTableName();
        Logger::Debug($this->tableName);
        $this->dbCollection = $dbServer->getCollection($this->tableName);
        Logger::Debug($this->dbCollection);
    }

    protected function getTableName()
    {
        if (isset($this->tableName)) {
            return $this->tableName;
        } else {
            // Use the class name as the default table name
            $className = get_class($this);
            $parts = explode('\\', $className);
            return strtolower(end($parts));
        }
    }

    public function Get($columns = [])
    {
        if (empty($columns)) {
            Logger::Debug($columns);
            return $this->dbCollection->GetAll();
        } else {
            // Implement a method in DBCollection to get specific columns
            // For now, we'll just return all columns
            Logger::Warning("Fetching specific columns is not implemented yet. Returning all columns.");
            return $this->dbCollection->GetAll();
        }
    }

    public function Filter($criteria)
    {
        return $this->dbCollection->FilterBy($criteria);
    }

    public function FindOne($column, $value)
    {
        return $this->dbCollection->FindOne($column, $value);
    }

    public function Insert($data)
    {
        return $this->dbCollection->Insert($data);
    }

    public function Update($data, $criteria)
    {
        return $this->dbCollection->Update($data, $criteria);
    }

    // Method to set a custom table name
    protected function setTableName($tableName)
    {
        $this->tableName = $tableName;
      
    }
}




?>