<?php

namespace System\MVC;

use System\Database\DBServer;
use System\Database\DBCollection;
use System\Core\Logger;

abstract class BaseModel
{
    protected $dbCollection;
    protected $tableName;

    public function __construct()
    {
        $dbServer = DBServer::connect();
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
            $className = get_class($this);
            $parts = explode('\\', $className);
            return strtolower(end($parts));
        }
    }

    public function getAll($columns = [])
    {
        if (empty($columns)) {
            Logger::Debug($columns);
            return $this->dbCollection->GetAll();
        } else {
            Logger::Warning("Fetching specific columns is not implemented yet. Returning all columns.");
            return $this->dbCollection->GetAll();
        }
    }

    public function filterBy($criteria)
    {
        return $this->dbCollection->FilterBy($criteria);
    }

    public function findOne($column, $value)
    {
        return $this->dbCollection->FindOne($column, $value);
    }

    public function insert($data)
    {
        return $this->dbCollection->Insert($data);
    }

    public function update($data, $criteria)
    {
        return $this->dbCollection->Update($data, $criteria);
    }

    public function delete($criteria)
    {
        return $this->dbCollection->Delete($criteria);
    }
}