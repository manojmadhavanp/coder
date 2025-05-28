<?php

namespace System\HTTP;

class TemplateEngine {
    private $template;
    private $data;

    public function __construct($template, $data)
    {
       $this->template = $template;
       $this->data = $data; 
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function render() {
        extract($this->data);
        ob_start();
        require($this->template);
        $output = ob_get_clean();

        return $output;
    }
}



