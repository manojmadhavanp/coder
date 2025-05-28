<?php

namespace System\Core;

use System\Core\Form;
use System\Database\DBServer;

class DBForm extends Form
{
    private $tableName;

    public function __construct($tableName, $action = '', $method = 'post')
    {
        parent::__construct($action, $method);
        $this->tableName = $tableName;
        $this->generateFields();
    }

    private function generateFields()
    {
        // Assuming you have a method to get the table structure
        $tableDescription = $this->getTableDescription($this->tableName);

        foreach ($tableDescription as $column => $details) {
            $type = $details['type'];
            $label = ucfirst($column);
            $required = isset($details['required']) && $details['required'];
            $options = [];

            if (isset($details['foreign_key'])) {
                echo 'Foreign key: ';
                print_r($details['foreign_key']);
                // Assuming you have a method to get options for foreign keys
                $options = $this->getForeignKeyOptions($details['foreign_key']);
                $type = 'select';
            }

            $this->addField($column, $type, $label, $required, $options);
        }
    }

    private function getTableDescription($tableName)
    {
        $db = DBServer::connect();
        $description = $db->describeTable($tableName);
        return $description;
    }

    private function getForeignKeyOptions($foreignKey)
    {
        // Implement logic to retrieve options for foreign key fields
        // This is a placeholder for actual database interaction
        // Example return array:

        //generate the code to get the relational foreignkey information and get the code to get the id and name from the table


        return [
            '1' => 'Option 1',
            '2' => 'Option 2',
            // Add other options as necessary
        ];
    }
}
