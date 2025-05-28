<?php

namespace System\MVC;

use System\Core\Logger;

class CollectionController
{
    protected static $instance;
    protected static $collections = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function register(string $name, string $className, string $tablename)
    {
        if (!isset(self::$collections[$name])) {
            self::$collections[$name]['class'] = $className;
            self::$collections[$name]['tablename'] = $tablename;
            Logger::Info("Registered collection: $name");
        } else {
            Logger::Warning("Collection $name already registered");
        }
    }

    public static function getCollection(string $name)
    {
        return self::$collections[$name];
    }
    public static function getCollections()
    {
        return self::$collections;
    }
    public static function getCollectionNames()
    {
        return array_keys(self::$collections);
    }
    
    public static function getAll()
    {
        return self::$collections;
    }
}