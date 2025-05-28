<?php

namespace System\Core;

class Form
{
    private $action;
    private $method;
    private $fields;
    private $submitButtonText;

    public function __construct($action = '', $method = 'post')
    {
        $this->action = $action;
        $this->method = $method;
        $this->fields = [];
        $this->submitButtonText = 'Submit';
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function addField($name, $type, $label, $required = false, $options = [])
    {
        $this->fields[] = [
            'name' => $name,
            'type' => $type,
            'label' => $label,
            'required' => $required,
            'options' => $options
        ];
        return $this;
    }

    public function setSubmitButtonText($text)
    {
        $this->submitButtonText = $text;
        return $this;
    }

    public function getHtml()
    {
        $html = "<form action=\"{$this->action}\" method=\"{$this->method}\">\n";
        
        foreach ($this->fields as $field) {
            $html .= $this->generateField($field);
        }
        
        $html .= "    <input type=\"submit\" value=\"{$this->submitButtonText}\">\n";
        $html .= "</form>";
        
        return $html;
    }

    private function generateField($field)
    {
        $html = "<div>\n";
        $html .= "    <label for=\"{$field['name']}\">{$field['label']}:</label>\n";
        
        if ($field['type'] === 'select' && !empty($field['options'])) {
            $html .= $this->generateSelect($field);
        } else {
            $html .= $this->generateInput($field);
        }
        
        $html .= "</div>\n";
        
        return $html;
    }

    private function generateSelect($field)
    {
        $html = "    <select id=\"{$field['name']}\" name=\"{$field['name']}\"" . ($field['required'] ? ' required' : '') . ">\n";
        
        foreach ($field['options'] as $value => $label) {
            $html .= "        <option value=\"$value\">$label</option>\n";
        }
        
        $html .= "    </select>\n";
        
        return $html;
    }

    private function generateInput($field)
    {
        $type = $this->getInputType($field['type']);
        $html = "    <input type=\"$type\" id=\"{$field['name']}\" name=\"{$field['name']}\"";
        
        if ($field['required']) {
            $html .= ' required';
        }
        
        $html .= ">\n";
        
        return $html;
    }

    private function getInputType($type)
    {
        switch (strtolower($type)) {
            case 'int':
            case 'integer':
            case 'smallint':
            case 'tinyint':
            case 'mediumint':
            case 'bigint':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime-local';
            case 'time':
                return 'time';
            case 'email':
                return 'email';
            case 'password':
                return 'password';
            default:
                return 'text';
        }
    }

    public static function generate(array $mainTable, array $supportingTables = []): string
    {
        $form = new self();

        foreach ($mainTable as $column => $details) {
            $type = $details['type'];
            $label = ucfirst($column);
            $required = isset($details['required']) && $details['required'];
            $options = [];

            if (isset($details['foreign_key']) && isset($supportingTables[$details['foreign_key']])) {
                $type = 'select';
                $options = $supportingTables[$details['foreign_key']];
            }

            $form->addField($column, $type, $label, $required, $options);
        }

        return $form->getHtml();
    }
}
