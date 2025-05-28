<?php

namespace System\Database;

use Exception;
use System\Core\Logger;

use PDO;
class DBCollection{
    private $pdo;
    private $table;

    public function __construct(PDO $pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    

    public function GetAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function FindOne($columnOrConditions, $value = null)
    {
        // Initialize the where clause and parameters
        $whereClause = [];
        $params = [];

        // Check if the first argument is an array (multiple conditions)
        if (is_array($columnOrConditions)) {
            foreach ($columnOrConditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[":{$column}"] = $value; // Bind the value to the parameter
            }
        } elseif (is_string($columnOrConditions) && $value !== null) {
            // Single column-value pair
            $whereClause[] = "{$columnOrConditions} = :value";
            $params[':value'] = $value; // Bind the value to the parameter
        } else {
            throw new Exception("InvalidArgumentException Invalid arguments provided to FindOne.");    
        }

        // Create the SQL query
        $whereString = implode(' AND ', $whereClause);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$whereString} LIMIT 1");
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function FilterBy($criteria, $tableName = null)
    {
        $table = $tableName ?? $this->table;
        $whereClause = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $whereClause[] = "$key = ?";
            $params[] = $value;
        }

        $whereString = implode(' AND ', $whereClause);
        $query = "SELECT * FROM $table WHERE $whereString";

        Logger::Debug("Executing query: " . $query . " with params: " . implode(', ', $params));

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            Logger::Error("Database error: " . $e->getMessage());
            throw $e;
        }
    }

    public function Insert($data)
    {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");
        return $stmt->execute($data);
    }

    public function Update($data, $criteria)
    {
        $setPart = [];
        foreach ($data as $column => $value) {
            $setPart[] = "{$column} = :set_{$column}";
        }
        $setPart = implode(", ", $setPart);

        $wherePart = [];
        foreach ($criteria as $column => $value) {
            $wherePart[] = "{$column} = :where_{$column}";
        }
        $wherePart = implode(" AND ", $wherePart);

        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$setPart} WHERE {$wherePart}");
        foreach ($data as $column => $value) {
            $stmt->bindValue(":set_{$column}", $value);
        }
        foreach ($criteria as $column => $value) {
            $stmt->bindValue(":where_{$column}", $value);
        }
        return $stmt->execute();
    }

    public function Delete($criteria)
    {
        $wherePart = [];
        foreach ($criteria as $column => $value) {
            $wherePart[] = "{$column} = :{$column}";
        }
        $wherePart = implode(" AND ", $wherePart);

        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$wherePart}");
        return $stmt->execute($criteria);
    }
}

?>