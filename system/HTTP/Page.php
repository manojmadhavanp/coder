<?php

namespace System\HTTP;

use System\HTTP\TemplateEngine;
use System\Core\Logger;

class Page
{
    protected $templatepath = TEMPLATE_PATH . "default.tpl";
    protected $template = "I AM A ERROR";
    protected $content;
    protected $pageData = [];
   

    public function __construct()
    {
        $this->templatepath = $this->ospath(TEMPLATE_PATH . "default.tpl");
       
        if (file_exists($this->templatepath)) {
            $this->template = file_get_contents($this->templatepath);
        }
        $this->content = "";
        $this->setData('title', 'Made from EzyPHP - Default page');
        $this->setData('desc', 'Made from EzyPHP - Default page');
        $this->setData('keywords', '');
    }

    public function template(string $usertemplatepath): bool
    {
        $templatepath = $this->ospath(TEMPLATE_PATH.$usertemplatepath.".tpl");
        if(file_exists($templatepath)){
            $this->templatepath = $templatepath;
            return true;
        }
        return false;
    }
    
    public function title(string $pagetitle){
        return $this->setData('title', strlen($pagetitle) < 60 ? $pagetitle : $this->getData('title'));
    }

    public function description(string $pagedescription)
    {
        return $this->setData('desc', strlen($pagedescription) < 160 ? $pagedescription : $this->getData('desc'));
    }

    public function keywords(string $pagekeywords)
    {
        return $this->setData('keywords', strlen($pagekeywords) > 0 ? $pagekeywords : $this->getData('keywords'));
    }

    public function content($page, $data)
    {
        Logger::Debug("Entering content method");
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
        
       /* echo '<pre>';
        print_r($this->pageData);
        echo '</pre>';*/
        $page = str_replace('/', DIRECTORY_SEPARATOR, $page);
        $filepath = $this->ospath($page . ".php");

        if (file_exists($filepath)) {
            $engine = new TemplateEngine($filepath, $this->pageData);
            $this->content = $engine->render();
        } else {
            echo "FILE NOT FOUND:" . $filepath;
        }
    }
    public function active_uri($uri){
        $this->pageData['active_uri']  = $uri;
    }
    private function ospath($path){
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $path = str_replace('/', '\\', $path);
        }
        return $path;
    }

    public function Render($filename){
        if (file_exists($this->templatepath)) {
            $this->setData('content', $this->content);
            $engine = new TemplateEngine($this->templatepath, $this->pageData);
            return $engine->render();
        }
    }

    public function loadView($viewPath)
    {
        $fullPath = $this->ospath(VIEW_PATH . $viewPath . '.php');
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }
        return false;
    }

    // Helper method to set data
    public function setData($key, $value)
    {
        $this->pageData[$key] = $value;
    }

    // Helper method to get data
    public function getData($key)
    {
        return $this->pageData[$key] ?? null;
    }

    // Helper method to get all data
    protected function getAllData()
    {
        return $this->pageData;
    }
}